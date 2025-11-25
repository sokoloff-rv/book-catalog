<?php

use app\models\Author;
use yii\bootstrap5\LinkPager;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\AuthorSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Авторы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="author-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->user->can('manageAuthors')): ?>
        <p>
            <?= Html::a('Добавить автора', ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    <?php endif; ?>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => [
            'class' => LinkPager::class,
            'options' => ['class' => 'pagination justify-content-center mt-4'],
        ],
        'columns' => [
            [
                'attribute' => 'full_name',
                'label' => 'ФИО',
            ],
            [
                'label' => 'Книги',
                'value' => function (Author $model) {
                    $bookCount = $model->getBook()->count();

                    return Html::a(
                        $bookCount,
                        ['book/index', 'BookSearch' => ['author_id' => $model->id]]
                    );
                },
                'format' => 'raw',
                'contentOptions' => ['style' => 'width: 160px'],
            ],
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Author $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 },
                'visibleButtons' => [
                    'update' => fn () => Yii::$app->user->can('manageAuthors'),
                    'delete' => fn () => Yii::$app->user->can('manageAuthors'),
                ],
            ],
        ],
    ]); ?>


</div>
