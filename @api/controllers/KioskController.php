<?php

namespace api\controllers;

use Yii;
use common\models\Store;
use yii\rest\Controller;
use common\models\Queue;
use yii\data\Pagination;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

class KioskController extends Controller
{

    public  function actionRequest($id)
    {
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
}
