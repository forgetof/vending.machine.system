<?php

use yii\helpers\Html;
use yii\grid\GridView;
/* @var $this yii\web\View */
/* @var $model common\models\Item */

$this->title = 'Create Item';

?>
<div class="item-create">
    <div class="row">
        <h1 class="col-sm-12">
            <?= Html::encode($this->title) ?>
        </h1>
    </div>

    <?= $this->render('_form_create', [
        'model' => $model,
    ]) ?>

</div>