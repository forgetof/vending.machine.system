<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Url;


/**
 * This is the model class for table "item".
 * @property int $id
 * @property string $name
 * @property double $price
 * @property int $box_id
 * @property int $store_id
 * @property smallint $status
 * @property int $created_at
 * @property int $updated_at
 */
class Item extends \yii\db\ActiveRecord
{

    public $sku;
    // public $checkbox = false;

    const STATUS_AVAILABLE = 0;
    const STATUS_VOID = 8;
    const STATUS_LOCKED = 9;
    const STATUS_SOLD = 10;
    const STATUS_DELIVERED = 7;

    public static function tableName()
    {
        return 'item';
    }

    // public  function getItemid()
    // {
    //     if ($checkbox = true) {
    //         return $this->id;
    //     }

    //     if ($checkbox = false) {
    //         return 0;
    //     }
    // }


    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }


    public function rules()
    {
        return [
            [['box_id', 'product_id'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['data_json'], 'string'],
            [['price'], 'number'],
            [['sku'], 'safe'],
            [['product_id'], 'integer'],
            [['status'], 'default', 'value' => self::STATUS_AVAILABLE],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'price' => 'Price',
            'created_at' => 'Created Time',
            'updated_at' => 'Updated Time',
            'box_id' => 'Box ID',
            'product_id' => 'Product ID'
        ];
    }

    public function getImageUrl()
    {
        return $this->product->getImageUrl();
    }

    // public function getPrice()
    // {
    //     if (!empty($this->product->price)) {
    //         return $this->product->price;
    //     }

    //     return null;
    // }

    public function getStoreId()
    {
        if (!empty($this->box->store_id)) {
            return $this->box->store_id;
        }

        return null;
    }

    public function getStatusText()
    {
        switch ($this->status) {
            case self::STATUS_AVAILABLE:
                $text = "Available";
                break;
            case self::STATUS_LOCKED:
                $text = "On Hold";
                break;
            case self::STATUS_SOLD:
                $text = "Sold";
                break;
            case self::STATUS_VOID:
                $text = "Removed";
                break;
            default:
                $text = "(Undefined)";
                break;
        }

        return $text;
    }

    // FOR ITEM INDEX 
    public function getPricing()
    {
        $num = number_format($this->price, 2);

        return 'RM ' . $num;
    }

    public function getStore()
    {
        return $this->hasOne(Store::class, ['id' => 'store_id'])->via('box');
    }

    public function getBox()
    {
        return $this->hasOne(Box::class, ['id' => 'box_id']);
    }

    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    public function getName()
    {
        if (!empty($this->product->name)) {
            return $this->product->name;
        }
    }

    public function getIsAvailable()
    {
        if ($this->status == Item::STATUS_SOLD) {
            return false;
        }

        if ($this->status == Item::STATUS_LOCKED) {
            return false;
        }

        return true;
    }
}
