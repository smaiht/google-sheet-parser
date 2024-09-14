<?php

namespace app\models;

use yii\mongodb\ActiveRecord;

class Product extends ActiveRecord
{
    public static function collectionName()
    {
        return 'products';
    }

    public function attributes()
    {
        return ['_id', 'name', 'category_id'];
    }
}