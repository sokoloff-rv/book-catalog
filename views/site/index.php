<?php

/** @var yii\web\View $this */

use yii\bootstrap5\Html;

$this->title = 'Добро пожаловать в каталог книг';
?>
<div class="site-index py-5">
    <div class="p-5 mb-5 bg-light rounded-3 border">
        <div class="container-fluid py-3">
            <p class="text-uppercase text-muted fw-semibold mb-2">Yii2 + MySQL</p>
            <h1 class="display-5 fw-bold mb-3">Каталог книг</h1>
            <p class="fs-5 mb-4">
                Подписка на авторов для гостей, управление книгами и авторами для зарегистрированных пользователей, отчёт по самым продуктивным авторам за выбранный год.
            </p>
            <div class="d-flex flex-wrap gap-3">
                <?= Html::a('Войти', ['/site/login'], ['class' => 'btn btn-primary btn-lg']) ?>
                <span class="align-self-center text-muted">или продолжить как гость для просмотра каталога</span>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="h-100 p-4 border rounded-3 bg-white shadow-sm position-relative">
                <h3 class="h5">Книги</h3>
                <p class="mb-3 text-muted">
                    Каталог книг с названием, годом выпуска, описанием, ISBN и обложкой.
                </p>
                <?= Html::a('Перейти к списку книг', ['/book/index'], ['class' => 'stretched-link']) ?>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="h-100 p-4 border rounded-3 bg-white shadow-sm position-relative">
                <h3 class="h5">Авторы</h3>
                <p class="mb-3 text-muted">
                    Ищите книги по авторам, подписывайтесь на тех, кто вам нравится.
                </p>
                <?= Html::a('Перейти к списку авторов', ['/author/index'], ['class' => 'stretched-link']) ?>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="h-100 p-4 border rounded-3 bg-white shadow-sm position-relative">
                <h3 class="h5">Топ-10</h3>
                <p class="mb-3 text-muted">
                    ТОП-10 авторов по числу выпущенных книг за выбранный год.
                </p>
                <?= Html::a('Открыть отчёт', ['#'], ['class' => 'stretched-link disabled', 'tabindex' => -1, 'aria-disabled' => 'true']) ?>
            </div>
        </div>
    </div>
</div>
