<?php

/** @var yii\web\View $this */
/** @var app\models\TopAuthorsReportForm $model */
/** @var int[] $availableYears */
/** @var yii\data\ArrayDataProvider $dataProvider */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Alert;
use yii\grid\GridView;

$this->title = 'Топ-10 авторов';
?>
<div class="report-top-authors py-4">
    <div class="mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <p class="text-uppercase text-muted fw-semibold mb-1">Отчёт</p>
            <h1 class="h3 mb-0">Топ-10 авторов по количеству книг</h1>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <?php $form = ActiveForm::begin([
                'method' => 'get',
                'options' => ['class' => 'row g-3 align-items-end'],
            ]); ?>
            <div class="col-auto">
                <?= $form->field($model, 'year')->dropDownList(
                    $availableYears ? array_combine($availableYears, $availableYears) : [],
                    [
                        'prompt' => 'Выберите год',
                        'class' => 'form-select',
                        'onchange' => 'this.form.submit();',
                    ]
                )->label('Год издания'); ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <?php if (empty($availableYears)): ?>
        <?= Alert::widget([
            'options' => ['class' => 'alert alert-warning'],
            'body' => 'В каталоге пока нет книг для построения отчёта.',
        ]) ?>
    <?php else: ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'summary' => false,
            'tableOptions' => ['class' => 'table table-striped table-bordered align-middle'],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn', 'header' => '#'],
                [
                    'attribute' => 'full_name',
                    'label' => 'Автор',
                ],
                [
                    'attribute' => 'book_count',
                    'label' => 'Книг за выбранный год',
                    'contentOptions' => ['class' => 'text-center'],
                ],
            ],
        ]) ?>
    <?php endif; ?>
</div>
