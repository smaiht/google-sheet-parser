<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $data array */
/* @var $error string */

$this->title = 'Google Sheet Data';
?>
<h1><?= Html::encode($this->title) ?></h1>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= Html::encode($error) ?></div>

<?php elseif (!empty($data)): ?>

    <table class="table">
        <?php foreach ($data as $row): ?>
            <tr>
                <?php foreach ($row as $cell): ?>
                    <td 
                        <?= $cell['bold'] ? ' style="font-weight: bold;"' : '' ?>
                        <?= $cell['colored'] ? ' style="background-color: #f0f0f0;"' : '' ?>
                        <?= isset($cell['changed']) ? ' style="background-color: yellow;"' : '' ?>
                    >
                        <?= Html::encode($cell['value']) ?>
                    </td>
                    <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </table>

<?php else: ?>

    <p>No data available.</p>
<?php endif; ?>