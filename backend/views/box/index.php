<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Boxes';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?php Html::a('Create Box', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            [
                'label' => 'Box Code',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->boxcode;
                }
            ],
            'store_id',
            'product.name',
            'created_at:datetime',
            'updated_at:datetime',
            ['class' => 'yii\grid\ActionColumn', 'header' => 'Action'],
        ],
    ]); ?>
</div>