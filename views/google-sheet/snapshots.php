<?php

use yii\helpers\Html;

$this->title = 'Snapshots';
?>
<h1><?= Html::encode($this->title) ?></h1>

<?php if (count($snapshots) > 0): ?>
    <ul>
        <?php foreach ($snapshots as $snapshot): ?>
            <li>
                <?= Html::a(date('Y-m-d H:i:s', $snapshot->created_at), ['snapshot', 'id' => (string)$snapshot->_id]) ?>
            </li>
        <?php endforeach; ?>
    </ul>

<?php else: ?>
    <p>No snapshots found.</p>
<?php endif; ?>