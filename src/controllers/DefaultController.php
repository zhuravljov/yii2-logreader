<?php

namespace zhuravljov\yii\logreader\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use zhuravljov\yii\logreader\Log;

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
        return $this->render('index', [
            'logs' => $this->module->getLogs(),
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
        $log = $this->find($slug, $stamp);
        if ($log->isExist) {
            return Yii::$app->response->sendFile($log->fileName, $log->downloadName, ['inline' => true]);
        } else {
            throw new NotFoundHttpException('Log not found.');
        }
    }

    public function actionArchive($slug)
    {
        if ($this->find($slug, null)->archive(date('YmdHis'))) {
            return $this->redirect(['history', 'slug' => $slug]);
        } else {
            throw new NotFoundHttpException('Log not found.');
        }
    }

    public function actionHistory($slug)
    {
        $log = $this->find($slug, null);
        $logs = [];
        foreach (glob(Log::extractFileName($log->alias, '*')) as $fileName) {
            $logs[] = new Log($log->name, $log->alias, Log::extractFileStamp($log->alias, $fileName));
        }

        usort($logs, function(Log $a, Log $b) {
            if ($a->updatedAt < $b->updatedAt) return 1;
            if ($a->updatedAt > $b->updatedAt) return -1;
            return 0;
        });

        return $this->render('history', [
            'name' => $log->name,
            'logs' => $logs,
        ]);
    }

    /**
     * @param string $slug
     * @param null|string $stamp
     * @return Log
     * @throws NotFoundHttpException
     */
    protected function find($slug, $stamp)
    {
        if ($log = $this->module->findLog($slug, $stamp)) {
            return $log;
        } else {
            throw new NotFoundHttpException('Log not found.');
        }
    }
}
