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
        
        $categories = array_filter($request->post('categories', []), function($value) {
            return $value !== '';
        });
        
        $products = array_filter($request->post('products', []), function($value) {
            return $value !== '';
        });
        
        $months = array_filter($request->post('months', []), function($value) {
            return $value !== '';
        });
        

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