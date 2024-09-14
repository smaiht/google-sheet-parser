<?php

namespace app\services;

use Yii;
use Google\Client;
use Google\Service\Sheets;
use app\models\Category;
use app\models\Product;
use app\models\Budget;
use app\models\Snapshot;

class GoogleSheetService
{
    private $service;
    private $spreadsheetId = '12C0J3jgShF_Uti7mxtIfldME4XficzFRwL2gE2Ek2YI';
    // private $spreadsheetId = '10En6qNTpYNeY_YFTWJ_3txXzvmOA7UxSCrKfKCFfaRw'; // OG
    private $sheetName = 'MA';
    private $range = 'A1:N106';
    private $formatData;
    private $monthStartIndex = 1;
    private $monthEndIndex = 12;

    public function __construct()
    {
        $client = new Client();
        $client->setAuthConfig(Yii::getAlias('@app/config/sheet-parser-435517-2624a8e5587b.json'));
        $client->setScopes([Sheets::SPREADSHEETS_READONLY]);
        $this->service = new Sheets($client);
    }

    public function processSheetData()
    {
        $this->getSheetData();

        $lastSnapshot = Snapshot::find()->orderBy(['created_at' => SORT_DESC])->one();
        $newChangesFound = [];

        if ($lastSnapshot) {
            $newChangesFound = $this->compareWithLastSnapshot($lastSnapshot->data);
        }

        if (empty($this->formatData)) {
            return ['error' => 'No data found.'];
        }

        $this->saveDataToDatabase();

        $newSnapshot = null;
        if (!empty($newChangesFound) || !$lastSnapshot) {
            $newSnapshot = $this->saveSnapshot($newChangesFound);
        }

        return [
            'data' => $this->formatData,
            'newSnapshot' => $newSnapshot,
        ];
    }

    private function getSheetData()
    {
        // values
        $response = $this->service->spreadsheets_values->get(
            $this->spreadsheetId,
            $this->sheetName . '!' . $this->range,
            ['majorDimension' => 'ROWS']
        );
        $values = $response->getValues();
    
        // formattings
        $response = $this->service->spreadsheets->get($this->spreadsheetId, [
            'ranges' => $this->sheetName . '!' . $this->range,
            'includeGridData' => true,
        ]);
        $gridData = $response->getSheets()[0]->getData()[0]->getRowData();
    
        $this->formatData = [];
        foreach ($values as $rowIndex => $row) {
            $formattedRow = [];
            foreach ($row as $cellIndex => $cellValue) {
                $cellData = $gridData[$rowIndex]->getValues()[$cellIndex] ?? null;
                $textFormat = $cellData ? $cellData->getEffectiveFormat()->getTextFormat() : null;
                $backgroundColor = $cellData ? $cellData->getEffectiveFormat()->getBackgroundColor() : null;
                $formattedRow[] = [
                    'value' => $cellValue,
                    'bold' => $textFormat ? $textFormat->getBold() : false,
                    'colored' => $this->isColoredBackground($backgroundColor),
                ];
            }
            $this->formatData[] = $formattedRow;
        }
    }
    
    private function isColoredBackground($backgroundColor)
    {
        if (!$backgroundColor) {
            return false;
        }

        // if bg-color != white
        return (
            $backgroundColor->getRed() != 1 
            || $backgroundColor->getGreen() != 1 
            || $backgroundColor->getBlue() != 1
        );
    }

    private function compareWithLastSnapshot($lastSnapshotData)
    {
        $changes = [];

        foreach ($this->formatData as $rowIndex => &$row) {
            foreach ($row as $cellIndex => &$cell) {
                if (isset($lastSnapshotData[$rowIndex][$cellIndex])) {
                    $lastCell = $lastSnapshotData[$rowIndex][$cellIndex];
                    if ($cell['value'] !== $lastCell['value']) {

                        if (!(strtolower($row[0]['value']) == 'total' || strtolower($lastSnapshotData[2][$cellIndex]['value']) == 'total')) { // hardcoded to skip 'total' values
                            $cell['changed'] = true;

                            $changes[] = [
                                'row' => $rowIndex,
                                'cell' => $cellIndex,
                                'old_value' => $lastCell['value'],
                                'new_value' => $cell['value'],
                            ];
                        }
                    }
                }
            }
        }

        return $changes;
    }

    private function saveDataToDatabase()
    {
        $currentCategory = null;

        $months = array_slice($this->formatData[2], $this->monthStartIndex, $this->monthEndIndex); // get months from the 3rd line
        $months = array_column($months, 'value');

        foreach ($this->formatData as $rowIndex => $row) {
            if ($rowIndex === 0) continue; // skip headline

            $itemName = $row[0]['value'] ?? '';
            if (
                empty($itemName) 
                || strtolower($itemName) === 'total'
            ) {
                continue; // skip empty rows and 'totals'
            }

            $isBold = $row[0]['bold'];
            $isColored = $row[0]['colored'];
            
            if ($isBold && $isColored) {
                // => category
                $currentCategory = $this->getOrCreateCategory($itemName);

            } elseif (!$isBold) {
                // => product
                $product = $this->getOrCreateProduct($itemName, $currentCategory->_id);

                $this->saveProductBudget($product, $row, $months);
            }
        }
    }

    private function getOrCreateCategory($name)
    {
        $category = Category::findOne(['name' => $name]);
        if (!$category) {
            $category = new Category(['name' => $name]);
            $category->save();
        }
        return $category;
    }

    private function getOrCreateProduct($name, $categoryId)
    {
        $product = Product::findOne(['name' => $name, 'category_id' => $categoryId]);
        if (!$product) {
            $product = new Product([
                'name' => $name,
                'category_id' => $categoryId
            ]);
            $product->save();
        }
        return $product;
    }

    private function saveProductBudget($product, $row, $months)
    {
        for ($i = 1; $i <= count($months); $i++) {
            $monthValue = isset($row[$i]['value']) 
                ? sprintf("%.2f", floatval(str_replace(['$', ',', ' '], '', $row[$i]['value']))) 
                : 0;
    
            $budget = Budget::findOne([
                'product_id' => $product->_id,
                'year' => date('Y'), // no year data on the sheet so...
                'month' => strtolower($months[$i-1])
            ]);
            if (!$budget) {
                $budget = new Budget();
            }
            $budget->product_id = $product->_id;
            $budget->year = date('Y');
            $budget->month = strtolower($months[$i-1]);
            $budget->value = $monthValue;
            $budget->save();
        }
    }

    private function saveSnapshot($newChangesFound)
    {
        $snapshot = new Snapshot();
        $snapshot->data = $this->formatData;
        $snapshot->created_at = time();
        $snapshot->changes = $newChangesFound;
        $snapshot->save();

        return $snapshot;
    }
}