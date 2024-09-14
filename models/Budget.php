<?php

namespace app\models;

use yii\mongodb\ActiveRecord;

class Budget extends ActiveRecord
{
    public static function collectionName()
    {
        return 'budgets';
    }

    public function attributes()
    {
        return ['_id', 'product_id', 'year', 'month', 'value'];
    }
}