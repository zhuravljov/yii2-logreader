<?php
/**
 * @var \yii\web\View $this
 * @var string $name
 * @var \yii\data\ArrayDataProvider $dataProvider
 */

use yii\grid\GridView;
use yii\helpers\Html;
use zhuravljov\yii\logreader\Log;

$this->title = $name;
$this->params['breadcrumbs'][] = ['label' => 'Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $name;
?>
<div class="logreader-history">
    <?= GridView::widget([
        'tableOptions' => ['class' => 'table'],
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'fileName',
                'format' => 'raw',
                'value' => function (Log $log) {
                    return pathinfo($log->fileName, PATHINFO_BASENAME);
                },
            ],
            [
                'attribute' => 'counts',
                'format' => 'raw',
                'value' => function (Log $log) {
                    return $this->render('_counts', ['log' => $log]);
                },
            ],
            [
                'attribute' => 'size',
                'format' => 'shortSize',
                'headerOptions' => ['class' => 'sort-ordinal'],
            ],
            [
                'attribute' => 'updatedAt',
                'format' => 'relativeTime',
                'headerOptions' => ['class' => 'sort-numerical'],
            ],
            [
                'class' => '\yii\grid\ActionColumn',
                'template' => '{view}',
                'urlCreator' => function ($action, Log $log) {
                    return [$action, 'slug' => $log->slug];
                },
                'buttons' => [
                    'view' => function ($url) {
                        return Html::a('View', $url, [
                            'class' => 'btn btn-xs btn-default',
                            'target' => '_blank',
                        ]);
                    },
                ],
            ],
        ],
    ]) ?>
</div>
<?php
$this->registerCss(<<<CSS

.logreader-history .table tbody td {
vertical-align: middle;
}

CSS
);