<?php

use backend\helpers\GridHelper;
use common\models\Apple;
use kartik\grid\GridView;
use kartik\select2\Select2;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var Apple $model */
/** @var GridHelper $gridHelper */

?>

<?php Pjax::begin(); ?>

    <div class="card">
        <div class="card-header">
            <h5>Управление яблоком</h5>
        </div>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'title',

                [
                    'attribute' => 'color',
                    'label' => $model->getAttributeLabel('color'),
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
                    'value' => fn (Apple $model) => $gridHelper->getColor($model->color),
                ],

                [
                    'attribute' => 'status',
                    'label' => $model->getAttributeLabel('status'),
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
                    'value' => fn (Apple $model) => $gridHelper->getStatus($model->status),
                ],

                [
                    'attribute' => 'eat_part',
                    'label' => $model->getAttributeLabel('eat_part'),
                    'value' => fn (Apple $model) => $model->eat_part . '%',
                ],

                [
                    'attribute' => 'condition',
                    'label' => $model->getAttributeLabel('condition'),
                    'value' => fn (Apple $model) => $gridHelper->getCondition((array) $model->attributes),
                ],

                [
                    'attribute' => 'created_at',
                    'label' => $model->getAttributeLabel('created_at'),
                    'value' => fn (Apple $model) =>
                    ($model->created_at) ? date('d.m.Y H:i', strtotime($model->created_at)) : null,
                ],

                [
                    'attribute' => 'dropped_at',
                    'label' => $model->getAttributeLabel('dropped_at'),
                    'value' => fn (Apple $model) =>
                    ($model->dropped_at) ? date('d.m.Y H:i', strtotime($model->dropped_at)) : null,
                ],
            ],
        ]) ?>

        <?php $form = ActiveForm::begin([
            'id' => 'appleForm'
        ]); ?>

            <div class="card-body">

                <div class="alert alert-light-secondary" role="alert">
                    <p class="txt-secondary"></p>
                </div>

                <?= $form->field($model, 'action')->radioList([
                    1 => 'Сорвать',
                    2 => 'Съесть',
                    3 => 'Отметить испорченным'
                ], [
                    'item' => function ($index, $label, $name, $checked, $value) {
                        $check = $checked ? ' checked="checked"' : '';
                        return "<label class=\"form__param\">
                                    <input type=\"radio\" name=\"$name\" value=\"$value\"$check> <i></i> 
                                $label</label>";
                    }]) ?>

                <?= $form->field($model, 'title')
                    ->textInput(['maxlength' => 100])
                    ->label($model->getAttributeLabel('title')
                        . ' <span class="txt-secondary mb-0"><strong>*</strong></span>') ?>

                <?= $form->field($model, 'bite')
                    ->textInput(['maxlength' => 3])
                    ->label($model->getAttributeLabel('bite')
                        . ' <span class="txt-secondary mb-0"><strong>*</strong></span>') ?>

            </div>

            <?= Html::submitButton('Сохранить', [
                'class' => 'btn btn-pill btn-success-gradien btn-lg',
                'data-bs-original-title' => 'btn btn-pill btn-success-gradien',
                'name' => 'submit',
                'value' => true
            ]) ?>

        <?php ActiveForm::end(); ?>

    </div>

<?php Pjax::end(); ?>
