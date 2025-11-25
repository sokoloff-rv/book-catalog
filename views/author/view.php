<?php

use app\models\Subscription;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Author $model */

$this->title = $model->full_name;
$this->params['breadcrumbs'][] = ['label' => 'Авторы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="author-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="d-flex gap-2 flex-wrap mb-3">
        <?php if (Yii::$app->user->can('manageAuthors')): ?>
            <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы уверены, что хотите удалить эту запись?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif; ?>

        <?= Html::button('Подписаться на автора', [
            'class' => 'btn btn-outline-success',
            'data-bs-toggle' => 'modal',
            'data-bs-target' => '#subscriptionModal',
            'data-subscription-authors' => Json::htmlEncode([
                ['id' => $model->id, 'name' => $model->full_name],
            ]),
            'data-return-url' => Yii::$app->request->url,
        ]) ?>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'full_name',
            [
                'label' => 'Книги',
                'value' => Html::a(
                    $model->getBook()->count(),
                    ['book/index', 'BookSearch' => ['author_id' => $model->id]]
                ),
                'format' => 'raw',
            ],
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
