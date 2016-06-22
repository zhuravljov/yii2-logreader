<?php
/**
 * @var \yii\web\View $this
 * @var array $counts
 */

use yii\helpers\Html;

/** @var \zhuravljov\yii\logreader\Module $module */
$module = $this->context->module;

foreach ($counts as $level => $count) {
    if (isset($module->levelClasses[$level])) {
        $class = $module->levelClasses[$level];
    } else {
        $class = $module->defaultLevelClass;
    }
    echo Html::tag('span', $count, [
        'class' => 'label ' . $class,
        'title' => $level,
    ]);
    echo ' ';
}
