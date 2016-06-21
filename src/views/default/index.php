<?php
/**
 * @var \yii\web\View $this
 * @var array $logs
 */

use yii\helpers\Html;

$this->title = 'Logs';
$this->params['breadcrumbs'][] = 'Logs';

/** @var \zhuravljov\yii\logreader\Module $module */
$module = $this->context->module;
?>
<div class="logreader-default-index">
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Counts</th>
                <th>Size</th>
                <th>Updated</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $info): ?>
                <tr>
                    <td>
                        <h5>
                            <?= Html::encode($info['name']) ?><br/>
                            <small><?= Html::encode($info['fileName']) ?></small>
                        </h5>
                    </td>
                    <td>
                        <?php
                        foreach ($info['counts'] as $level => $count) {
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
                        ?>
                    </td>
                    <td>
                        <?= Yii::$app->formatter->asShortSize($info['size']) ?>
                    </td>
                    <td>
                        <?= Yii::$app->formatter->asRelativeTime($info['updated']) ?>
                    </td>
                    <td>
                        <?= Html::a('View', ['view', 'slug' => $info['slug']], ['target' => '_blank']) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
$this->registerCss(<<<CSS

.logreader-default-index .table tbody td {
    vertical-align: middle;
}

CSS
);