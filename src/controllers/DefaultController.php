<?php

namespace zhuravljov\yii\logreader\controllers;

use Yii;
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
                'alias' => $alias,
                'fileName' => $fileName = Yii::getAlias($alias),
                'exist' => $exist = file_exists($fileName),
                'size' => $exist ? filesize($fileName) : null,
                'updated' => $exist ? filemtime($fileName) : null,
                'counts' => $this->module->getLogCounts($fileName),
            ];
        }

        return $this->render('index', [
            'logs' => $logs,
        ]);
    }

    /**
     * @param string $slug
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($slug)
    {
        $fileName = Yii::getAlias($this->find($slug));
        if (file_exists($fileName)) {
            return Yii::$app->response->sendFile($fileName, "$slug.log", ['inline' => true]);
        } else {
            throw new NotFoundHttpException('Log not found.');
        }
    }

    public function actionHistory($slug)
    {

    }

    public function actionArchive($slug)
    {

    }

    /**
     * @param string $slug
     * @return string log alias
     * @throws NotFoundHttpException
     */
    protected function find($slug)
    {
        foreach ($this->module->aliases as $name => $alias) {
            if ($slug === Inflector::slug($name)) {
                return $alias;
            }
        }
        throw new NotFoundHttpException('Log not found.');
    }
}
