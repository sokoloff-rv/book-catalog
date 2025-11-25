<?php

use yii\helpers\Html;
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

    <?php if (Yii::$app->user->can('manageBooks')): ?>
        <p>
            <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы уверены, что хотите удалить эту запись?',
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    <?php endif; ?>

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

</div>
