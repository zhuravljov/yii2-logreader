<?php

namespace zhuravljov\yii\logreader;

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
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
