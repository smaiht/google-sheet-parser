<?php

use yii\helpers\Html;

$this->title = 'Change Logs';
?>
<h1><?= Html::encode($this->title) ?></h1>

<?php foreach ($snapshots as $snapshot): ?>
    <h2>Snapshot: <?= date('Y-m-d H:i:s', $snapshot->created_at) ?></h2>
    <?php if (count($snapshot->changes) > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Row</th>
                    <th>Cell</th>
                    <th>Old Value</th>
                    <th>New Value</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($snapshot->changes as $change): ?>
                    <tr>
                        <td><?= $change['row'] ?></td>
                        <td><?= $change['cell'] ?></td>
                        <td><?= $change['old_value'] ?></td>
                        <td><?= $change['new_value'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No changes in this snapshot.</p>
    <?php endif; ?>
<?php endforeach; ?>