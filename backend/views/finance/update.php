<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Finance */

$this->title = 'Update Finance: ' . $model->id;

?>

<div class="finance-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>