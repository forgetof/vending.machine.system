<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\User;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User';
// $this->params['breadcrumbs'][] = $this->title;
?>

<div class="User-index">

    <div class="card">
        <div class="pull-right text-right">
            <?= Html::a('Create New User', ['create'], ['class' => 'btn btn-success']) ?>
        </div>

        <div style="max-width:440px">
            <?php echo $this->render('_search', ['model' => $searchModel]); ?>
        </div>
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
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'username',
                    'filterInputOptions' => [
                        'class'  => 'form-control',
                        'placeholder' => 'Search....'
                    ]
                ],
                [
                    'header' => 'Roles',
                    'format' => 'raw',
                    'value' => function ($model) {

                        $roles = Yii::$app->authManager->getRolesByUser($model->id);

                        if ($roles) {
                            $array = array_keys($roles);
                            return $array[0];
                        }

                        return "User";
                    }
                ],
                [
                    'attribute' => 'Permission',
                    'format' => 'raw' ,
                    'value' => function($model) {

                        $roles = Yii::$app->authManager->getPermissionsByUser($model->id);

                        if ($roles) {
                            $arrays = array_keys($roles);
        
                            return implode(" ", $arrays);
                        }

                        return '<span class="text-danger">' .'No Permission'.'';
                    }
                ],
                [
                    'attribute' => 'status',
                    'format' => 'raw',
                    'value' => function ($model) {
                        if ($model->status == User::STATUS_ACTIVE) {
                            return '<span class="text-success">' . 'Active' . '</span>';
                        }

                        return '<span class="text-danger">' . 'Inactive' . '</span>';
                    }
                ],
                [
                    'attribute' => '',
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'col-lg-1',],
                    'value' => function ($model) {
                        return Html::a('view', ['/user/view', 'id' => $model->id]);
                    }
                ],
            ],
        ]); ?>
    </div>
</div>