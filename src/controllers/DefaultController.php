<?php

namespace zhuravljov\yii\logreader\controllers;

use Yii;
use yii\caching\FileDependency;
use yii\helpers\Inflector;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Default controller for the `logreader` module
 */
class DefaultController extends Controller
{
    /**
     * @var \zhuravljov\yii\logreader\Module
     */
    public $module;

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $logs = [];
        foreach ($this->module->aliases as $name => $alias) {
            $logs[] = [
                'name' => $name,
                'slug' => Inflector::slug($name),
                'file' => $fileName = $this->getFileName($alias),
                'exist' => $exist = file_exists($fileName),
                'size' => $exist ? filesize($fileName) : null,
                'updated' => $exist ? filemtime($fileName) : null,
                'counts' => $this->getLogCounts($fileName),
            ];
        }

        return $this->render('index', [
            'logs' => $logs,
        ]);
    }

    /**
     * @param string $slug
     * @param string $stamp
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($slug, $stamp = null)
    {
        $fileName = $this->getFileName($this->find($slug)[1], $stamp);
        if (file_exists($fileName)) {
            $name = $slug;
            if ($stamp !== null) {
                $name .= '.' . $stamp;
            }
            return Yii::$app->response->sendFile($fileName, "$name.log", ['inline' => true]);
        } else {
            throw new NotFoundHttpException('Log not found.');
        }
    }

    public function actionArchive($slug)
    {
        $alias = $this->find($slug)[1];

        $fileName = $this->getFileName($alias);
        if (!file_exists($fileName)) {
            throw new NotFoundHttpException('Log not found.');
        }

        rename($fileName, $this->getFileName($alias, date('YmdHis')));
        return $this->redirect(['history', 'slug' => $slug]);
    }

    public function actionHistory($slug)
    {
        list($name, $alias) = $this->find($slug);
        $logs = [];
        foreach (glob($this->getFileName($alias, '*')) as $fileName) {
            $logs[] = [
                'file' => $fileName,
                'stamp' => $this->getStamp($alias, $fileName),
                'size' => filesize($fileName),
                'updated' => filemtime($fileName),
                'counts' => $this->getLogCounts($fileName),
            ];
        }

        uasort($logs, function($a, $b) {
            if ($a['updated'] < $b['updated']) return -1;
            if ($a['updated'] > $b['updated']) return 1;
            return 0;
        });

        return $this->render('history', [
            'name' => $name,
            'slug' => $slug,
            'logs' => $logs,
        ]);
    }

    /**
     * @param string $slug
     * @return string[] name and alias
     * @throws NotFoundHttpException
     */
    protected function find($slug)
    {
        foreach ($this->module->aliases as $name => $alias) {
            if ($slug === Inflector::slug($name)) {
                return [$name, $alias];
            }
        }
        throw new NotFoundHttpException('Log not found.');
    }

    /**
     * @param string $alias
     * @param null|string $stamp
     * @return string
     */
    protected function getFileName($alias, $stamp = null)
    {
        $fileName = Yii::getAlias($alias, false);
        if ($stamp === null) {
            return $fileName;
        }

        $info = pathinfo($fileName);
        return strtr('{dir}/{name}.{stamp}.{ext}', [
            '{dir}' => $info['dirname'],
            '{name}' => $info['filename'],
            '{ext}' => $info['extension'],
            '{stamp}' => $stamp,
        ]);
    }

    /**
     * @param string $alias
     * @param string $fileName
     * @return string|null
     */
    protected function getStamp($alias, $fileName)
    {
        $originName = Yii::getAlias($alias, false);
        $origInfo = pathinfo($originName);
        $fileInfo = pathinfo($fileName);
        if (
            $origInfo['dirname'] === $fileInfo['dirname'] &&
            $origInfo['extension'] === $fileInfo['extension'] &&
            strpos($fileInfo['filename'], $origInfo['filename']) === 0
        ) {
            return substr($fileInfo['filename'], strlen($origInfo['filename']) + 1);
        } else {
            return null;
        }
    }

    /**
     * @param string $fileName
     * @param bool $force
     * @return array
     */
    protected function getLogCounts($fileName, $force = false)
    {
        if (!file_exists($fileName)) return [];

        $key = $fileName . '#counts';
        if (!$force && ($counts = Yii::$app->cache->get($key)) !== false) {
            return $counts;
        }

        $counts = [];
        if ($h = fopen($fileName, 'r')) {
            while (($line = fgets($h)) !== false) {
                if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $line)) {
                    if (preg_match('/^[\d\-\: ]+\[.*\]\[.*\]\[.*\]\[(.*)\]/U', $line, $m)) {
                        $level = $m[1];
                        if (!isset($counts[$level])) $counts[$level] = 0;
                        $counts[$level]++;
                    }
                }
            }
            fclose($h);
            Yii::$app->cache->set($key, $counts, 0, new FileDependency(['fileName' => $fileName]));
        }
        return $counts;
    }
}
