<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\grid\GridView;
use common\models\User;
use common\models\Store;
use common\models\Item;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use common\models\Box;
use common\models\product;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Store */

?>

<div class="store-view">
    <div class="card">
        <div class="pull-right text-right">
            <?= Html::a('Create Box', ['box/create', 'id' => $model->id], ['class' => 'btn btn-sm btn-primary']) ?>
            <?= Html::a('Open All Boxes', ['box/open-all-box', 'id' => $model->id], [
                'class' => 'btn btn-sm btn-danger',
                'data' => [
                    'confirm' => 'Make sure to open all boxes?',
                    'method' => 'post'
                ]
            ]); ?>
        </div>

        <div style="max-width:440px">
            <b><?= $model->name ?></b><br>
            <?= $model->address . PHP_EOL; ?>

        </div>
    </div>

    <div class=" alert alert-info " style="margin:0 0 12px">
        <p>
            Total Number of Empty Box(es): <b><?= $boxSearch->getEmptyBoxQuantity($model->id) ?></b>
        </p>
    </div>

    <div class="card">
        <?= GridView::widget([
            'tableOptions' => [
                'class' => 'table   table-bordered  table-hover ',
            ],
            'options' => [
                'class' => 'table-responsive',
            ],
            'dataProvider' => $dataProvider,
            'rowOptions' => function ($model) {
                if ($model->status == Box::BOX_STATUS_EMPTY) {
                    return ['style' => 'background-color:#ffcccb'];
                }
            },
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'code',
                    'label' => 'Box',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->getBoxCode() . "<br>" . $model->getStatusText();
                    }
                ],
                [
                    'attribute' => 'name',
                    'label' => 'Item',
                    'value' => 'product.name',
                ],
                'item.price:currency',
                [
                    'label' => 'Action',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->getAction();
                    }
                ],
            ],
        ]); ?>
    </div>
</div>