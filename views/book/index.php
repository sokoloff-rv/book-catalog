<?php

use app\models\Book;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\BookSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Книги';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="book-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->user->can('manageBooks')): ?>
        <p>
            <?= Html::a('Добавить книгу', ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    <?php endif; ?>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'label' => 'Обложка',
                'value' => function (Book $model) {
                    return $model->cover_path
                        ? Html::img($model->cover_path, ['class' => 'img-thumbnail', 'style' => 'max-width: 90px'])
                        : Html::tag('span', 'Нет обложки', ['class' => 'text-muted']);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'title',
                'label' => 'Название',
            ],
            [
                'attribute' => 'publish_year',
                'label' => 'Год выпуска',
                'contentOptions' => ['style' => 'width: 130px'],
            ],
            [
                'attribute' => 'isbn',
                'label' => 'ISBN',
                'contentOptions' => ['style' => 'width: 160px'],
            ],
            [
                'attribute' => 'description',
                'label' => 'Описание',
                'value' => function (Book $model) {
                    return $model->description
                        ? StringHelper::truncateWords($model->description, 30, '…')
                        : 'Описание не добавлено';
                },
                'format' => 'ntext',
            ],
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Book $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 },
                'visibleButtons' => [
                    'update' => fn () => Yii::$app->user->can('manageBooks'),
                    'delete' => fn () => Yii::$app->user->can('manageBooks'),
                ],
            ],
        ],
    ]); ?>


</div>
