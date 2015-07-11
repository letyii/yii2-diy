<?php
use kartik\grid\GridView;
use yii\bootstrap\Html;
?>
<?=
Html::a('Create', ['/diy/default/create'], ['class' => 'btn btn-success', 'style' => 'margin: 20px 0;']);
?>
<div>
<?php
    echo GridView::widget([
    'panel' => [
        'heading' => Yii::t(Yii::$app->controller->module->id, 'Diy'),
        'tableOptions' => [
            'id' => 'listDefault',
        ],
    ],
    'pjax' => TRUE,
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        ['class' => 'kartik\grid\CheckboxColumn'],
        '_id',
        'title',
//        [
//            'attribute' => 'create_time',
//            'filterType' => GridView::FILTER_DATE_RANGE,
//            'format' => 'raw',
//            'width' => '270px',
//            'filterWidgetOptions' => [
//                'pluginOptions' => [
//                    'format' => 'Y-m-d',
//                    'separator' => ' to ',
//                    'opens' => 'left'
//                ],
//                'presetDropdown' => true,
//                'hideInput' => true,
//                'convertFormat' => true,
//            ],
//            'value' => function ($model, $key, $index, $widget) {
//                return date('d/m/Y', $model->create_time->sec);
//            },
//        ],
        [
            'attribute' => 'status',
            'class' => '\app\components\BooleanColumn',
        //                'trueLabel' => 'Yes',
        //                'falseLabel' => 'No'
        ],
        [
            'class' => '\kartik\grid\ActionColumn',
        ],
    ],
    'responsive' => true,
    'hover' => true,
]);
?>
</div>