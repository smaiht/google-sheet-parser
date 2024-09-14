<?php

namespace app\models;

use yii\mongodb\ActiveRecord;

class Snapshot extends ActiveRecord
{
    public static function collectionName()
    {
        return 'snapshots';
    }

    public function attributes()
    {
        return ['_id', 'data', 'created_at', 'changes'];
    }
}