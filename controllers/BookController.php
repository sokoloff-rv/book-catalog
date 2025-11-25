<?php

namespace app\controllers;

use app\models\Author;
use app\models\Book;
use app\models\BookSearch;
use app\models\SmsLog;
use app\models\Subscription;
use app\services\SmsServiceInterface;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BookController implements the CRUD actions for Book model.
 */
class BookController extends Controller
{
    public function __construct($id, $module, private readonly SmsServiceInterface $smsService, $config = [])
    {
        parent::__construct($id, $module, $config);
    }

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'only' => ['create', 'update', 'delete'],
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['manageBooks'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Book models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new BookSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Book model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Book model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Book();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                $this->notifySubscribers($model);

                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'authorList' => $this->getAuthorList(),
        ]);
    }

    /**
     * Updates an existing Book model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'authorList' => $this->getAuthorList(),
        ]);
    }

    /**
     * Deletes an existing Book model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Book model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Book the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Book::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Returns a list of authors for form elements.
     *
     * @return array<int, string>
     */
    private function getAuthorList(): array
    {
        return Author::find()
            ->select('full_name')
            ->orderBy('full_name')
            ->indexBy('id')
            ->column();
    }

    private function notifySubscribers(Book $book): void
    {
        $authorIds = $book->authorIds ?: $book->getAuthors()->select('id')->column();

        if (!$authorIds) {
            return;
        }

        $subscriptions = Subscription::find()
            ->where(['author_id' => $authorIds])
            ->all();

        if (!$subscriptions) {
            return;
        }

        $authorNames = Author::find()
            ->select('full_name')
            ->where(['id' => $authorIds])
            ->column();

        $message = sprintf(
            'Новая книга "%s" (%s). Авторы: %s.',
            $book->title,
            $book->publish_year,
            $authorNames ? implode(', ', $authorNames) : 'неизвестно'
        );

        foreach ($subscriptions as $subscription) {
            $status = 'skipped';
            $raw = null;

            try {
                $result = $this->smsService->send($subscription->phone, $message);
                $status = $result['status'] ?? 'unknown';
                $raw = $result['raw'] ?? null;
            } catch (\Throwable $exception) {
                $status = 'exception';
                $raw = $exception->getMessage();
            }

            $log = new SmsLog([
                'subscription_id' => $subscription->id,
                'book_id' => $book->id,
                'sent_at' => time(),
                'status' => $status,
                'provider_raw' => $raw ? (is_string($raw) ? $raw : json_encode($raw)) : null,
            ]);

            if (!$log->save()) {
                Yii::error('Не удалось сохранить лог SMS: ' . json_encode($log->errors));
            }
        }
    }
}
