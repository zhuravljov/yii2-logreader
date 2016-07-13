<?php
/**
 * @var \yii\web\View $this
 * @var string $name
 * @var \zhuravljov\yii\logreader\Log[] $logs
 */

use yii\helpers\Html;

$this->title = $name;
$this->params['breadcrumbs'][] = ['label' => 'Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $name;
?>
<div class="logreader-history">
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
                    <?= Html::encode(pathinfo($log->fileName, PATHINFO_BASENAME)) ?><br/>
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
                    <?= Html::a('View', ['view', 'slug' => $log->slug, 'stamp' => $log->stamp], [
                        'class' => 'btn btn-xs btn-default',
                        'target' => '_blank'
                    ]) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
$this->registerCss(<<<CSS

.logreader-history .table tbody td {
vertical-align: middle;
}

CSS
);