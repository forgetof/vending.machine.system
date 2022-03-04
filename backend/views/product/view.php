<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Product */

$this->title = $model->name;

\yii\web\YiiAsset::register($this);
?>
<div class="product-view">
    <div class="card">
        <h1><?= Html::encode($this->title) ?></h1>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'sku',
                'name',
                'description',
                'price:currency',
                'cost:currency',
                'image',
                [
                    'attribute' => 'image',
                    'value' => $model->imageUrl,
                    'format' => ['image', ['width' => '250', 'height' => '250']],
                ],
                'created_at:datetime',
                'updated_at:datetime',
            ],
        ]) ?>

        <div class="form-group">
            <?= Html::a('Back', ['product/index'], ['class' => 'btn btn-danger']) ?>
        </div>
    </div>
</div>