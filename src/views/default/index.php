<?php
/**
 * @var \yii\web\View $this
 * @var array $logs
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
            <?php foreach ($logs as $info): ?>
                <tr>
                    <td>
                        <h5>
                            <?= Html::encode($info['name']) ?><br/>
                            <small><?= Html::encode($info['file']) ?></small>
                        </h5>
                    </td>
                    <td>
                        <?= $this->render('_counts', ['counts' => $info['counts']]) ?>
                    </td>
                    <td>
                        <?= Yii::$app->formatter->asShortSize($info['size']) ?>
                    </td>
                    <td>
                        <?= Yii::$app->formatter->asRelativeTime($info['updated']) ?>
                    </td>
                    <td>
                        <?= Html::a('History', ['history', 'slug' => $info['slug']], [
                            'class' => 'btn btn-xs btn-default',
                        ]) ?>
                        <?php if ($info['exist']): ?>
                            <?= Html::a('View', ['view', 'slug' => $info['slug']], [
                                'class' => 'btn btn-xs btn-default',
                                'target' => '_blank'
                            ]) ?>
                            <?= Html::a('Archive', ['archive', 'slug' => $info['slug']], [
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