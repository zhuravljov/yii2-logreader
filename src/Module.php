<?php

namespace zhuravljov\yii\logreader;

use Yii;
use yii\caching\FileDependency;

/**
 * LogReader module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'zhuravljov\yii\logreader\controllers';
    /**
     * @var array
     */
    public $aliases = [];
    /**
     * @var array
     */
    public $levelClasses = [
        'trace' => 'label-default',
        'info' => 'label-info',
        'warning' => 'label-warning',
        'error' => 'label-danger',
    ];
    /**
     * @var string
     */
    public $defaultLevelClass = 'label-default';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }

    /**
     * @param string $fileName
     * @param bool $force
     * @return array
     */
    public function getLogCounts($fileName, $force = false)
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
