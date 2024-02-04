<?php

use backend\helpers\GridHelper;
use common\models\Apple;
use kartik\grid\GridView;
use kartik\select2\Select2;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var Apple $filterModel */
/** @var ArrayDataProvider $dataProvider */
/** @var GridHelper $gridHelper */

$this->title = 'Яблоки';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="book-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать n-количество яблок', ['create-random'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php if (Yii::$app->session->hasFlash('success')) : ?>
        <div class="alert alert-light-success" role="alert">
            <div class="txt-success">
                <?php echo Yii::$app->session->getFlash('success'); ?>
            </div>
        </div>
    <?php endif;?>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $filterModel,
        'filterSelector' => 'select[name="per-page"]',
        'bordered' => true,
        'striped' => false,
        'condensed' => true,
        'responsive' => true,
        'hover' => true,
        'floatHeader' => true,
        'showPageSummary' => true,
        'headerRowOptions' => [
            'class' => 'kv-align-middle'
        ],
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => $this->title,
        ],
        'pager' => [
            'options' => [
                'class' => 'pagination pagination-primary pagin-border-primary'
            ],
            'firstPageLabel' => 'Первая',
            'lastPageLabel' => 'Последняя'
        ],
        'rowOptions'   => function ($model) {
            return ['data-url' => Url::to(['view', 'id' => $model['id']]), 'style' => 'cursor:pointer'];
        },
        'containerOptions' => [
            'style' => 'overflow: auto; vertical-align: middle',
            'class' => 'text-center kv-align-middle'],
        'toolbar' =>  [
            ['content' =>
                Html::a('Создать', ['create'], [
                    'id' => 'createRecord',
                    'data-pjax' => 1,
                    'class' => 'btn btn-success'
                ]) .
                Html::a(
                    'Сбросить фильтры',
                    ['/' . str_replace('/index', '', \Yii::$app->request->pathInfo)],
                    [
                        'id' => 'dropFilters',
                        'data-pjax' => 1,
                        'class' => 'btn btn-primary',
                    ]
                ) .
                '{export}'
            ],
        ],
        'toggleDataOptions' => [
            'all' => [
                'class' => 'btn btn-outline-primary', // 'btn btn-secondary' for BS4.x / BS5.x
                'title' => 'Все'
            ]
        ],
        'toggleDataContainer' => ['class' => ''],
        'exportContainer' => ['style' => 'display: none'],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],

            'id',

            [
                'attribute' => 'title',
                'label' => $filterModel->getAttributeLabel('title'),
            ],

            [
                'attribute' => 'color',
                'label' => $filterModel->getAttributeLabel('color'),
                'format' => 'html',
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => $gridHelper->getColors(),
                'filterOptions' => [
                    'style' => 'vertical-align: top;',
                ],
                'filterWidgetOptions' => [
                    'pluginOptions' => [
                        'hideSearch' => true,
                        'allowClear' => true
                    ],
                    'options' => ['prompt' => '', 'multiple' => true],
                    'theme' => Select2::THEME_BOOTSTRAP
                ],
                'value' => fn (array $model) => $gridHelper->getColor($model['color']),
            ],

            [
                'attribute' => 'status',
                'label' => $filterModel->getAttributeLabel('status'),
                'format' => 'html',
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => $gridHelper->getStatuses(),
                'filterOptions' => [
                    'style' => 'vertical-align: top;',
                ],
                'filterWidgetOptions' => [
                    'pluginOptions' => [
                        'hideSearch' => true,
                        'allowClear' => true
                    ],
                    'options' => ['prompt' => '', 'multiple' => true],
                    'theme' => Select2::THEME_BOOTSTRAP
                ],
                'value' => fn (array $model) => $gridHelper->getStatus($model['status']),
            ],

            [
                'attribute' => 'eat_part',
                'label' => $filterModel->getAttributeLabel('eat_part'),
                'value' => fn (array $model) => $model['eat_part'] . '%',
            ],

            [
                'attribute' => 'condition',
                'label' => $filterModel->getAttributeLabel('condition'),
                'value' => fn (array $model) => $gridHelper->getCondition($model),
            ],

            [
                'attribute' => 'created_at',
                'label' => $filterModel->getAttributeLabel('created_at'),
                'value' => fn (array $model) =>
                    ($model['created_at']) ? date('d.m.Y H:i', strtotime($model['created_at'])) : null,
            ],

            [
                'attribute' => 'dropped_at',
                'label' => $filterModel->getAttributeLabel('dropped_at'),
                'value' => fn (array $model) =>
                    ($model['dropped_at']) ? date('d.m.Y H:i', strtotime($model['dropped_at'])) : null,
            ],

            [
                'class' => 'kartik\grid\ActionColumn',
                'contentOptions' => [ 'style' => 'width: 120px'],
                'template' => '<div class="m-b-20"><div class="btn-group">{update}</div></div>',
                'buttons' => [
                    'update' => function ($url, $model) {
                        return Html::a('&#9998;', ['update', 'id' => $model['id']], [
                            'title' => Yii::t('app', 'Редактировать'),
                            'class' => 'btn btn-success'
                        ]);
                    },
                ]
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
