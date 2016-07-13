<?php
/**
 * @var \yii\web\View $this
 * @var \zhuravljov\yii\logreader\Log[] $logs
 */

use yii\helpers\Html;

$this->title = 'Logs';
$this->params['breadcrumbs'][] = 'Logs';
?>
<div class="logreader-index">
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
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td>
                        <h5>
                            <?= Html::encode($log->name) ?><br/>
                            <small><?= Html::encode(substr($log->fileName, 0)) ?></small>
                        </h5>
                    </td>
                    <td>
                        <?= $this->render('_counts', ['log' => $log]) ?>
                    </td>
                    <td>
                        <?= Yii::$app->formatter->asShortSize($log->size) ?>
                    </td>
                    <td>
                        <?= Yii::$app->formatter->asRelativeTime($log->updatedAt) ?>
                    </td>
                    <td>
                        <?= Html::a('History', ['history', 'slug' => $log->slug], [
                            'class' => 'btn btn-xs btn-default',
                        ]) ?>
                        <?php if ($log->isExist): ?>
                            <?= Html::a('View', ['view', 'slug' => $log->slug], [
                                'class' => 'btn btn-xs btn-default',
                                'target' => '_blank'
                            ]) ?>
                            <?= Html::a('Archive', ['archive', 'slug' => $log->slug], [
                                'class' => 'btn btn-xs btn-default',
                                'data' => ['method' => 'post', 'confirm' => 'Are you sure?'],
                            ]) ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
$this->registerCss(<<<CSS

.logreader-index .table tbody td {
    vertical-align: middle;
}

CSS
);