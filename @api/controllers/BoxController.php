<?php

namespace api\controllers;

use Yii;
use common\models\Box;
use yii\rest\Controller;
use common\models\Queue;
use common\models\Item;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

// BoxController implements the CRUD actions for Box model.
class BoxController extends Controller
{
    public function actionStoreItemIntoBox()
    {
        $store_id   = Yii::$app->request->getBodyParam('store_id');
        $code       = Yii::$app->request->getBodyParam('code');
        $data       = Yii::$app->request->getBodyparams();

        $box = Box::find()->where(['store_id' => $store_id])->andWhere(['code' => $code])->andWhere(['<>', 'status', 1])->one();

        if ($box) {
            $box->status = Box::BOX_STATUS_OCCUPIED;
            $box->save();

            $this->createStorageItem($box->id, $data);

            if ($box->save()) {
                return [
                    "code" => 1,
                    "message"   => "Success"
                ];
            }
        }

        return [
            "code" => -1,
            "message"   => "Failed"
        ];
    }

    public function actionOpenBox()
    {
        $store_id   = Yii::$app->request->getBodyParam('store_id');
        $code       = Yii::$app->request->getBodyParam('code');

        $box = Box::find()->where(['store_id' => $store_id])->andWhere(['code' => $code])->one();

        if ($box) {
            $hardware_id = $box->hardware_id;

            Queue::push($store_id, $hardware_id);

            $this->revokeStorageItem($box->id);

            $box->status = Box::BOX_STATUS_EMPTY;
            $box->save();

            return [
                "code" => 1,
                "message" => "Successful open for " . $hardware_id
            ];
        }

        return [
            "code" => -1,
            "message"   => "Box not found"
        ];
    }

    protected function createStorageItem($box_id, $data)
    {
        $item = new Item();
        $item->product_id = 1;
        $item->box_id = $box_id;
        $item->price = 99;
        $item->status = Item::STATUS_LOCKED;
        $item->data_json = Json::encode($data);

        return $item->save();
    }

    protected function revokeStorageItem($box_id)
    {
        $item = Item::find()->where(['box_id' => $box_id])->andWhere(['status' => Item::STATUS_LOCKED])->orderBy('created_at DESC')->one();

        if ($item) {
            $item->status = Item::STATUS_DELIVERED;

            return $item->save();
        }

        return false;
    }
}
