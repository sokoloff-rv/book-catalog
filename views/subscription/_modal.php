<?php

use app\models\Subscription;
use Yii;
use yii\bootstrap5\Modal;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var Subscription $subscriptionModel */

$modalId = 'subscriptionModal';

if (!Yii::$app->user->isGuest && empty($subscriptionModel->phone)) {
    $subscriptionModel->phone = Yii::$app->user->identity->phone;
}
?>

<?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
    <div class="alert alert-<?= Html::encode($type) ?>">
        <?= Html::encode($message) ?>
    </div>
<?php endforeach; ?>

<?php Modal::begin([
    'title' => 'Подписаться на автора',
    'id' => $modalId,
    'size' => Modal::SIZE_DEFAULT,
    'closeButton' => ['aria-label' => 'Закрыть'],
]); ?>
    <div class="subscription-form">
        <?php $form = ActiveForm::begin([
            'id' => 'subscription-form',
            'action' => ['/subscription/create'],
        ]); ?>

        <?= Html::hiddenInput('returnUrl', Yii::$app->request->url, ['data-role' => 'subscription-return-url']) ?>

        <?= $form->field($subscriptionModel, 'author_id', [
            'options' => ['class' => 'mb-3 d-none', 'data-role' => 'author-select-wrapper'],
        ])->dropDownList([], [
            'prompt' => 'Выберите автора',
            'data-role' => 'author-select',
        ])->hint('Выберите одного из авторов книги.') ?>

        <?= $form->field($subscriptionModel, 'phone')->textInput([
            'maxlength' => true,
            'placeholder' => '+79990000000',
        ]) ?>

        <div class="form-group">
            <?= Html::submitButton('Подписаться', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
<?php Modal::end(); ?>

<?php
$js = <<<JS
(function() {
    const modalId = '#{$modalId}';
    const modalEl = document.querySelector(modalId);
    if (!modalEl) {
        return;
    }

    const authorSelectWrapper = modalEl.querySelector('[data-role="author-select-wrapper"]');
    const authorSelect = modalEl.querySelector('[data-role="author-select"]');
    const returnUrlInput = modalEl.querySelector('[data-role="subscription-return-url"]');

    const prepareOptions = (authors) => {
        authorSelect.innerHTML = '';
        const promptOption = document.createElement('option');
        promptOption.value = '';
        promptOption.textContent = 'Выберите автора';
        authorSelect.appendChild(promptOption);

        authors.forEach(({id, name}) => {
            const option = document.createElement('option');
            option.value = id;
            option.textContent = name;
            authorSelect.appendChild(option);
        });
    };

    const setAuthor = (authors) => {
        if (!Array.isArray(authors)) {
            authors = [];
        }

        if (authors.length === 1) {
            authorSelectWrapper.classList.add('d-none');
            prepareOptions(authors);
            authorSelect.value = authors[0].id;
        } else {
            authorSelectWrapper.classList.remove('d-none');
            prepareOptions(authors);
            authorSelect.value = '';
        }
    };

    document.querySelectorAll('[data-subscription-authors]').forEach((button) => {
        button.addEventListener('click', () => {
            const authorsRaw = button.getAttribute('data-subscription-authors') || '[]';
            const authors = JSON.parse(authorsRaw);
            const returnUrl = button.getAttribute('data-return-url') || window.location.href;

            setAuthor(authors);
            returnUrlInput.value = returnUrl;
        });
    });

    authorSelect.addEventListener('change', (event) => {
        return event.target.value;
    });
})();
JS;
$this->registerJs($js);
?>
