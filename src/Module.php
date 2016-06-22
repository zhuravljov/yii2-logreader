<?php

namespace zhuravljov\yii\logreader;

use Yii;
use yii\base\BootstrapInterface;
use yii\base\InvalidConfigException;
use yii\web\Application;

/**
 * LogReader module definition class
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
            $app->getUrlManager()->addRules([
                $this->id => $this->id . '/default/index',
                $this->id . '/<action:\w+>/<slug:[\w-]+>' => $this->id . '/default/<action>',
                $this->id . '/<action:\w+>' => $this->id . '/default/<action>',
            ], false);
        } else {
            throw new InvalidConfigException('Can use for web application only.');
        }
    }
}
