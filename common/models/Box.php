<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\models\Queue;

/**
 * This is the model class for table "box".
 * @property int $id
 * @property int $code
 * @property int $status
 * @property int $store_id

 */
class Box extends \yii\db\ActiveRecord
{

    public $prefix;
    public $text;

    const BOX_STATUS_EMPTY = 10;
    const BOX_STATUS_OCCUPIED = 9;
    const BOX_STATUS_LOCK = 0;


    public static function tableName()
    {
        return 'box';
    }

    public function rules()
    {
        return [
            [['code', 'store_id'], 'integer'],
            [['code'], 'required'],
            [['name'], 'safe'],
            [['status'], 'default', 'value' => self::BOX_STATUS_EMPTY],
            [['hardware_id', 'data_json'], 'string'],
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    // public function getStore_id()
    // {
    //     if (!empty($this->store->id)) {
    //         return $this->store_id = $this->store->id;
    //     }

    //     return null;
    // }


    public function attributeLabels()
    {
        return [
            'id' => 'Box ID',
            'code' => 'Box Code',
            'status' => 'Box Status',
            'name' => 'Item Name',
            'store_id' => 'Store ID',
        ];
    }

    public function getAction()
    {
        if ($this->item) {
            return Html::a('Modify', ['/item/update', 'id' => $this->item->id], ['class' => 'btn btn-success']);
        }

        return Html::a('Restock', ['item/create', 'id' => $this->id], ['class' => 'btn btn-primary']);
    }


    public function getStatusText()
    {
        switch ($this->status) {
            case self::BOX_STATUS_EMPTY:
                $text = "Empty";
                break;
            case self::BOX_STATUS_LOCK:
                $text = "Lock box";
                break;
            case self::BOX_STATUS_OCCUPIED:
                $text = "Occupied";
                break;
            default:
                $text = "(Undefined)";
                break;
        }

        return $text;
    }

    public function getStore()
    {
        return $this->hasOne(Store::class, ['id' => 'store_id']);
    }

    public function getItem()
    {
        return $this->hasOne(Item::class, ['box_id' => 'id'])
            ->where(['item.status' => [Item::STATUS_AVAILABLE, Item::STATUS_LOCKED]]);
    }


    // public function getItems()
    // {
    //     return $this->hasMany(Item::class, ['box_id' => 'id']);
    // }

    // public function getAvailableItems()
    // {
    //     return $this->getItems()->orderBy(['id' => SORT_DESC])
    //         ->where(['item.status' => [Item::STATUS_AVAILABLE, Item::STATUS_LOCKED]])
    //         ->limit(1);
    // }

    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id'])->via('item');
    }


    public function getBoxcode()
    {
        if (empty($this->store->prefix)) {
            $text = $this->code;
        } else {
            $text = $this->store->prefix . $this->code;
        }

        return $text;
    }

    public function getItemPrice()
    {
        if ($this->item) {
            return $this->item->price;
        }

        return null;
    }

    public function getItemImageUrl()
    {
        if ($this->item) {
            return $this->item->getImageUrl();
        }

        return null;
    }

    public function getItemId()
    {
        if ($this->item) {
            return $this->item->id;
        }

        return null;
    }

    public static function previousItem($box_id)
    {
        $item = Item::find()->where([
            'box_id' => $box_id,
            'status' => Item::STATUS_SOLD,
        ])->orderBy([
            'created_at' => SORT_DESC,
        ])->one();

        if ($item) {
            return [
                'item_name' => $item->name,
                'sku'   => $item->product->sku
            ];
        }

        //If never sold Item before then return empty
        return [
            'item_name' => '',
            'sku'   => ''
        ];
    }
}
