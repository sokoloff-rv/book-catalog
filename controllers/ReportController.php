<?php

namespace app\controllers;

use app\models\TopAuthorsReportForm;
use app\services\AuthorReportService;
use Yii;
use yii\data\ArrayDataProvider;
use yii\db\Query;
use yii\web\Controller;

class ReportController extends Controller
{
    public function actionTopAuthors()
    {
        $availableYears = (new Query())
            ->select('publish_year')
            ->from('books')
            ->distinct()
            ->orderBy(['publish_year' => SORT_DESC])
            ->column();

        $model = new TopAuthorsReportForm();
        $model->availableYears = $availableYears;

        $defaultYear = $availableYears[0] ?? (int) date('Y');
        $model->year = $defaultYear;

        if ($model->load(Yii::$app->request->get()) && !$model->validate()) {
            $model->year = $defaultYear;
        }

        $reportService = new AuthorReportService();
        $topAuthors = $model->year ? $reportService->getTopAuthorsByYear((int) $model->year) : [];

        $dataProvider = new ArrayDataProvider([
            'allModels' => $topAuthors,
            'pagination' => false,
        ]);

        return $this->render('top-authors', [
            'model' => $model,
            'availableYears' => $availableYears,
            'dataProvider' => $dataProvider,
        ]);
    }
}
