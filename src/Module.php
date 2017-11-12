<?php

namespace zhuravljov\yii\logreader;

use Yii;
use yii\base\BootstrapInterface;
use yii\base\InvalidConfigException;
use yii\web\Application;
use yii\web\GroupUrlRule;

/**
 * LogReader module definition class
 *
 * @property Log[] $logs
 * @property integer $totalCount
 */
class Module extends \yii\base\Module implements BootstrapInterface
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
    public function bootstrap($app)
    {
        if ($app instanceof Application) {
            $app->getUrlManager()->addRules([[
                'class' => GroupUrlRule::class,
                'prefix' => $this->id,
                'rules' => [
                    '' => 'default/index',
                    '<action:\w+>/<slug:[\w-]+>' => 'default/<action>',
                    '<action:\w+>' => 'default/<action>',
                ],
            ]], false);
        } else {
            throw new InvalidConfigException('Can use for web application only.');
        }
    }

    /**
     * @return Log[]
     */
    public function getLogs()
    {
        $logs = [];
        foreach ($this->aliases as $name => $alias) {
            $logs[] = new Log($name, $alias);
        }

        return $logs;
    }

    /**
     * @param string $slug
     * @param null|string $stamp
     * @return null|Log
     */
    public function findLog($slug, $stamp)
    {
        foreach ($this->aliases as $name => $alias) {
            if ($slug === Log::extractSlug($name)) {
                return new Log($name, $alias, $stamp);
            }
        }

        return null;
    }

    /**
     * @param Log $log
     * @return Log[]
     */
    public function getHistory(Log $log)
    {
        $logs = [];
        foreach (glob(Log::extractFileName($log->alias, '*')) as $fileName) {
            $logs[] = new Log($log->name, $log->alias, Log::extractFileStamp($log->alias, $fileName));
        }

        return $logs;
    }

    /**
     * @return integer
     */
    public function getTotalCount()
    {
        $total = 0;
        foreach ($this->getLogs() as $log) {
            foreach ($log->getCounts() as $count) {
                $total += $count;
            }
        }

        return $total;
    }
}
