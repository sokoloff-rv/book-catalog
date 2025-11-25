<?php

use app\models\Author;
use yii\bootstrap5\LinkPager;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use app\models\Subscription;

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
                'value' => static fn (Author $model) => Html::a(Html::encode($model->full_name), ['view', 'id' => $model->id]),
                'format' => 'raw',
                'contentOptions' => ['class' => 'link-in-cell'],
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
                'label' => 'Подписка',
                'format' => 'raw',
                'contentOptions' => ['style' => 'width: 200px'],
                'value' => function (Author $model) {
                    return Html::button('Подписаться на автора', [
                        'class' => 'btn btn-outline-success btn-sm',
                        'data-bs-toggle' => 'modal',
                        'data-bs-target' => '#subscriptionModal',
                        'data-subscription-authors' => Json::htmlEncode([
                            ['id' => $model->id, 'name' => $model->full_name],
                        ]),
                        'data-return-url' => Yii::$app->request->url,
                    ]);
                },
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

    <?= $this->render('//subscription/_modal', [
        'subscriptionModel' => new Subscription(),
    ]) ?>


</div>
