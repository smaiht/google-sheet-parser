<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $result array */
/* @var $allCategories array */
/* @var $allProducts array */
/* @var $allMonths array */
/* @var $selectedCategories array */
/* @var $selectedProducts array */
/* @var $selectedMonths array */

$this->title = "Category Budget Analysis";

?>

<h1><?= Html::encode($this->title) ?></h1>

<div class="alert alert-info">
    <h4>Instructions:</h4>
    <p>Select categories, products, and months to analyze the budget data. You can choose multiple options in each dropdown with "Ctrl".</p>
</div>

<?php if (!empty($selectedCategories) || !empty($selectedProducts) || !empty($selectedMonths)): ?>
    <div class="alert alert-success">
        <h4>Current Selection:</h4>
        <?php if (!empty($selectedCategories)): ?>
            <p><strong>Categories:</strong> <?= implode(', ', $selectedCategories) ?></p>
        <?php else: ?>
            <p><strong>All Categories</strong></p>
        <?php endif; ?>

        <?php if (!empty($selectedProducts)): ?>
            <p><strong>Products:</strong> <?= implode(', ', $selectedProducts) ?></p>
        <?php else: ?>
            <p><strong>All Products</strong></p>
        <?php endif; ?>

        <?php if (!empty($selectedMonths)): ?>
            <p><strong>Months:</strong> <?= implode(', ', array_map('ucfirst', $selectedMonths)) ?></p>
        <?php else: ?>
            <p><strong>All Months</strong></p>
        <?php endif; ?>
    </div>
<?php endif; ?>


<?php $form = ActiveForm::begin(['method' => 'POST']); ?>

    <?= Html::dropDownList(
        'categories', 
        $selectedCategories, 
        array_combine($allCategories, $allCategories), 
        ['class' => 'form-control', 'multiple' => true, 'prompt' => 'Categories']
    ) ?>

    <?= Html::dropDownList(
        'products', 
        $selectedProducts, 
        array_combine($allProducts, $allProducts), 
        ['class' => 'form-control', 'multiple' => true, 'prompt' => 'Products']
    ) ?>

    <?= Html::dropDownList(
        'months', 
        $selectedMonths, 
        array_combine($allMonths, array_map('ucfirst', $allMonths)), 
        ['class' => 'form-control', 'multiple' => true, 'prompt' => 'Months']
    ) ?>

    <br />

    <?= Html::submitButton('Get Data', ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Reset', ['category-budget'], ['class' => 'btn btn-secondary']) ?>

<?php ActiveForm::end(); ?>

<br />

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Category</th>
            <th>Total Budget</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($result as $item): ?>
        <tr>
            <td><?= Html::encode($item['_id']) ?></td>
            <td>$<?= number_format($item['totalBudget'], 2) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
