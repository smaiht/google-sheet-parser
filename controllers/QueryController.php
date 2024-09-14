<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Budget;
use app\models\Category;
use app\models\Product;
use yii\helpers\ArrayHelper;

class QueryController extends Controller
{
    public function actionCategoryBudget()
    {
        $request = Yii::$app->request;
        
        $categories = $request->post('categories', []);
        $products = $request->post('products', []);
        $months = $request->post('months', []);

        $allCategories = Category::find()->select(['name'])->column();
        $allProducts = Product::find()->select(['name'])->column();
        $allMonths = ['january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december'];

        $pipeline = [];

        // $pipeline[] = ['$match' => ['year' => (string)$year]];

        if (!empty($months)) {
            $pipeline[] = ['$match' => ['month' => ['$in' => $months]]];
        }

        $pipeline[] = [
            '$lookup' => [
                'from' => 'products',
                'localField' => 'product_id',
                'foreignField' => '_id',
                'as' => 'product'
            ]
        ];
        $pipeline[] = ['$unwind' => '$product'];
        $pipeline[] = [
            '$lookup' => [
                'from' => 'categories',
                'localField' => 'product.category_id',
                'foreignField' => '_id',
                'as' => 'category'
            ]
        ];
        $pipeline[] = ['$unwind' => '$category'];

        if (!empty($categories)) {
            $pipeline[] = ['$match' => ['category.name' => ['$in' => $categories]]];
        }

        if (!empty($products)) {
            $pipeline[] = ['$match' => ['product.name' => ['$in' => $products]]];
        }

        $pipeline[] = [
            '$group' => [
                '_id' => '$category.name',
                'totalBudget' => ['$sum' => ['$toDouble' => '$value']]
            ]
        ];
        $pipeline[] = ['$sort' => ['totalBudget' => -1]];

        $result = Budget::getCollection()->aggregate($pipeline);

        return $this->render('categoryBudget', [
            'result' => $result,
            'allCategories' => $allCategories,
            'allProducts' => $allProducts,
            'allMonths' => $allMonths,
            'selectedCategories' => $categories,
            'selectedProducts' => $products,
            'selectedMonths' => $months,
        ]);
    }
}