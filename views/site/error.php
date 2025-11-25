<?php

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception $exception */

use yii\helpers\Html;

$this->title = $name;
?>
<div class="site-error">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
    </div>

    <p>
        При обработке запроса произошла ошибка.
    </p>
    <p>
        Если вы столкнулись с ошибкой сервера, сообщите об этом. Спасибо!
    </p>

</div>
