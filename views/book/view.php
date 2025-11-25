<?php

use app\models\Subscription;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Book $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Книги', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="book-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="d-flex gap-2 flex-wrap mb-3">
        <?php if (Yii::$app->user->can('manageBooks')): ?>
            <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы уверены, что хотите удалить эту запись?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif; ?>

        <?php
        $authorsForSubscription = array_map(
            fn ($author) => ['id' => $author->id, 'name' => $author->full_name],
            $model->authors
        );
        ?>

        <?= Html::button('Подписаться на автора', [
            'class' => 'btn btn-outline-success',
            'data-bs-toggle' => 'modal',
            'data-bs-target' => '#subscriptionModal',
            'data-subscription-authors' => Json::htmlEncode($authorsForSubscription),
            'data-return-url' => Yii::$app->request->url,
            'disabled' => empty($authorsForSubscription),
            'title' => empty($authorsForSubscription) ? 'У книги не указан автор' : null,
        ]) ?>
    </div>

    <div class="mb-4">
        <?php if ($model->coverUrl): ?>
            <?= Html::img($model->coverUrl, ['class' => 'img-responsive', 'style' => 'max-width: 100%; height: auto;']) ?>
        <?php else: ?>
            <p class="text-muted">Обложка не добавлена</p>
        <?php endif; ?>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            [
                'label' => 'Авторы',
                'value' => $model->authors
                    ? implode(', ', array_map(
                        fn ($author) => Html::a(Html::encode($author->full_name), ['author/view', 'id' => $author->id]),
                        $model->authors
                    ))
                    : 'Авторы не указаны',
                'format' => 'raw',
            ],
            'publish_year',
            'description:ntext',
            'isbn',
            'cover_path',
            [
                'attribute' => 'created_at',
                'format' => ['datetime', 'php:d.m.Y H:i'],
            ],
            [
                'attribute' => 'updated_at',
                'format' => ['datetime', 'php:d.m.Y H:i'],
            ],
        ],
    ]) ?>

    <?= $this->render('//subscription/_modal', [
        'subscriptionModel' => new Subscription(),
    ]) ?>

</div>
