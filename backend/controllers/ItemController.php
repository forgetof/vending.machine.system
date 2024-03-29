<?php

namespace backend\controllers;

use Yii;
use common\models\Item;
use common\models\Box;
use common\models\Queue;
use common\models\Product;
use common\models\Log;
use backend\models\ItemSearch;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

// ItemController implements the CRUD actions for Item model.
class ItemController extends Controller
{
    public $sku;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'view'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['update', 'create', 'void'],
                        'allow' => true,
                        'roles' => ['staff'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],

        ];
    }

    public function actionIndex()
    {
        $searchModel = new ItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate($id)
    {
        $model = new Item();
        $model->box_id = $id;

        if ($model->load(Yii::$app->request->post())) {
            $getsku = Product::find()->where(['sku' => $model->sku])->one();

            if ($getsku) {
                Log::push(
                    Yii::$app->user->identity->id,
                    'item',
                    'restock',
                    [
                        'store_name' => $model->store->name,
                        'box_code' => $model->box->code,
                        'item'  => $getsku->name
                    ]
                );

                $model->product_id = $getsku->id;

                if ($model->price <= 0) {
                    $model->price = $model->product->price;
                }

                if ($model->box->status == Box::BOX_STATUS_OCCUPIED) {
                    Yii::$app->session->setFlash('danger', 'This product has been added.');

                    return $this->redirect([
                        'store/view',
                        'id' => $model->box->store_id
                    ]);
                }

                if ($model->save()) {
                    $box = Box::findOne(['id' => $id]);

                    $box->status = Box::BOX_STATUS_OCCUPIED;
                    $box->save();

                    return $this->redirect([
                        'store/view',
                        'id' => $model->box->store_id
                    ]);
                }
            } else {
                Yii::$app->session->setFlash('error', 'Non exist item.');
            }
        }

        //Open box for restock.
        Queue::push($model->box->store_id, $model->box->hardware_id);

        $dataProvider = new ActiveDataProvider([
            'query' => Item::find()->where([
                'status' => [Item::STATUS_AVAILABLE, Item::STATUS_LOCKED],
                // 'store_id' => ($model->box->store_id)
            ]),
        ]);

        return $this->render('create', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->price <= 0) {
                $model->price = $model->product->price;
            }

            Log::push(
                Yii::$app->user->identity->id,
                'item',
                'update',
                [
                    'store_name' => $model->store->name,
                    'box_code' => $model->box->code,
                    'amount'    => $model->price
                ]
            );

            if ($model->save()) {
                return $this->redirect([
                    'store/view',
                    'id' => $model->box->store_id
                ]);
            }
        }

        $dataProvider = new ActiveDataProvider([
            'query' => Item::find()->where([
                'status' => [Item::STATUS_AVAILABLE, Item::STATUS_LOCKED],
                // 'store_id'=> ($model->box->store_id)
            ]),
        ]);

        return $this->render('update', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    // 废除产品：操作员强行下架产品
    public function actionVoid($id)
    {
        $model = $this->findModel($id);
        $model->status = Item::STATUS_VOID;
        $model->save();

        $box_model = $model->box;
        $box_model->status = Box::BOX_STATUS_EMPTY;
        $box_model->save();

        Log::push(
            Yii::$app->user->identity->id,
            'item',
            'void',
            [
                'store_name' => $model->store->name,
                'box_code' => $box_model->code,
                'item'  => $model->name,
                'sku'   => $model->product->sku
            ]
        );

        // open box for remove item
        Queue::push($model->box->store_id, $model->box->hardware_id);

        if ($model->save() && $box_model->save()) {
            return $this->redirect([
                'store/view',
                'id' => $model->box->store_id
            ]);
        } else {
            Yii::$app->session->setFlash('error', 'Error in removing item.');
        }
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->delete()) {
            return $this->redirect(['index']);
        }
    }

    protected function findModel($id)
    {
        if (($model = Item::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
