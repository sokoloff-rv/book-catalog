<?php

namespace app\controllers;

use app\models\Subscription;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

class SubscriptionController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['POST'],
                ],
            ],
        ];
    }

    public function actionCreate(): Response
    {
        $model = new Subscription();
        $request = Yii::$app->request;
        $model->load($request->post());
        $model->user_id = Yii::$app->user->isGuest ? null : (int) Yii::$app->user->id;

        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Подписка успешно оформлена.');
        } else {
            $errorText = $model->getFirstError('author_id')
                ?: $model->getFirstError('phone')
                ?: 'Не удалось оформить подписку.';
            Yii::$app->session->setFlash('error', $errorText);
        }

        $returnUrl = $request->post('returnUrl') ?: $request->referrer;

        return $this->redirect($returnUrl ?: ['/']);
    }
}
