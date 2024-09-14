<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use Yii;

class ParseBudgetController extends Controller
{
    const SNAPSHOT_BASE_URL = '/google-sheet/snapshot?id=';
    
    public function actionHello($message = 'hello world')
    {
        echo $message . $_ENV["TEST"] . "\n";

        return ExitCode::OK;
    }
    
    public function actionIndex()
    {
        $this->stdout("Starting budget parsing...\n");

        $result = Yii::$app->googleSheetService->processSheetData();

        if (isset($result['error'])) {
            $this->stderr("Error: " . $result['error'] . "\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->stdout("Budget parsing completed successfully.\n");

        if (!empty($result['newSnapshot'])) {
            $snapshotId = $result['newSnapshot']->_id;
            $snapshotUrl = self::SNAPSHOT_BASE_URL . (string)$snapshotId;

            if (!empty($result['newSnapshot']->changes)) {
                $this->stdout("Changes found. New snapshot created.\n");
            } else {
                $this->stdout("No changes found. New snapshot created.\n");
            }

            $this->stdout("Snapshot URL: " . $snapshotUrl . "\n");
        } else {
            $this->stdout("No changes found. No new snapshot created.\n");
        }

        return ExitCode::OK;
    }
    


}


