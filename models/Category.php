<?php

namespace app\models;

use yii\mongodb\ActiveRecord;

class Category extends ActiveRecord
{
    public static function collectionName()
    {
        return 'categories';
    }

    public function attributes()
    {
        return ['_id', 'name'];
    }
}