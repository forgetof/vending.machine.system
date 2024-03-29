<?php

namespace frontend\controllers;

use Yii;
use frontend\models\Box;
use common\models\Store;
use common\models\SaleRecord;
use frontend\components\Controller;
use common\models\Queue;
use common\models\Item;

// BoxController implements the CRUD actions for Box model.
class ApiController extends Controller
{

    public  function actionRequest($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $store = Store::find()->where(['id' => $id])->one();

        if (empty($store)) {
            return 'Store does not exist';
        }

        $priority_execution = Queue::find()->where([
            'store_id' => $id,
            'status' => Queue::STATUS_WAITING,
        ])->orderBy([
            'created_at' => SORT_ASC,
            'priority' => SORT_DESC
        ])->one();

        if ($priority_execution) {
            return  [
                'command' => $priority_execution->action
            ];
        }

        return  [
            'status' => 'ok'
        ];
    }

    public function actionNext($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $store = Store::find()->where(['id' => $id])->one();

        if (empty($store)) {
            return 'Store does not exist';
        }

        $priority_execution = Queue::find()->where([
            'store_id' => $id,
            'status' => Queue::STATUS_WAITING,
        ])->orderBy([
            'created_at' => SORT_ASC,
            'priority' => SORT_DESC
        ])->one();

        if ($priority_execution) {
            $priority_execution->updateAttributes([
                'status' => Queue::STATUS_SUCCESS
            ]);
        }

        return  [
            'status' => 'ok'
        ];
    }

    public function actionReference($payandgo_order_number, $vm_order_number)
    {
        $model =  SaleRecord::find()->where(['order_number' => $vm_order_number])->one();
        if ($model) {
            $model->updateReference($payandgo_order_number);
            return true;
        }

        return false;
    }

    public function actionCreate()
    {
        $item_id        = Yii::$app->request->getBodyParam('item_id');
        $reference_no   = Yii::$app->request->getBodyParam('reference_no');

        $item = Item::findOne($item_id);

        if ($item) {
            if ($item->getIsAvailable()) {
                $model = new SaleRecord();

                $model->item_id      = $item->id;
                $model->order_number = $item->store->prefix . $item->box->code . time();
                $model->box_id       = $item->box_id;
                $model->store_id     = $item->box->store_id;
                $model->sell_price   = $item->price;
                $model->unique_id    = $reference_no;
                $model->status       = SaleRecord::STATUS_INIT;
                $model->save();

                return $model;
            }

            return [
                'error' => 'Item is not available for purchase',
            ];
        }

        return [
            'error' => 'Item not found',
        ];
    }

    public function actionGetItem()
    {
        $store_id        = Yii::$app->request->getBodyParam('id');

        $box_model = Box::find()->where(['store_id' => $store_id, 'status' => Box::BOX_STATUS_OCCUPIED])->all();

        if ($box_model) {
            foreach ($box_model as $model) {
                $data[] = $model;
            }

            return $data;
        }

        return [
            'code'  => -1,
            'message' => 'item not find',
        ];
    }
}
