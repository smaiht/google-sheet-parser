<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use Google\Client;
use Google\Service\Sheets;
use app\models\Category;
use app\models\Product;
use app\models\Budget;
use app\models\Snapshot;
use yii\web\NotFoundHttpException;


class GoogleSheetController extends Controller
{
    public function actionIndex()
    {
        $result = Yii::$app->googleSheetService->processSheetData();
        return $this->render('index', $result);
    }

    public function actionLogs()
    {
        $snapshots = Snapshot::find()->orderBy(['created_at' => SORT_DESC])->all();
        return $this->render('logs', ['snapshots' => $snapshots]);
    }

    public function actionSnapshots()
    {
        $snapshots = Snapshot::find()->orderBy(['created_at' => SORT_DESC])->all();
        return $this->render('snapshots', ['snapshots' => $snapshots]);
    }

    public function actionSnapshot($id)
    {
        $snapshot = Snapshot::findOne($id);
        if ($snapshot) {
            return $this->render('index', ['data' => $snapshot->data]);
        } else {
            throw new NotFoundHttpException('The requested snapshot does not exist.');
        }
    }


}