<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\helpers\Url;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property string $name
 * @property double $price
 * @property string $image
 * @property int $created_at
 * @property int $updated_at
 */
class Product extends \yii\db\ActiveRecord
{
    public $imageFile;

    public static function tableName()
    {
        return 'product';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules()
    {
        return [
            [['name', 'price', 'sku', 'cost'], 'required'],
            ['sku', 'unique'],
            [['price', 'cost'], 'number'],
            [['name', 'description'], 'string', 'max' => 255],
            [['data_json'], 'string'],
            [['category'], 'string', 'max' => 20],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg', 'maxSize' => 1024 * 1024],
        ];
    }

    public function getCategories()
    {
        return [
            'food_beverage' => 'Food & Beverage',
            'home_living' => 'Home & Living',
            'electronic' => 'Electronic & Accessories',
            'mobile_accessory' => 'Mobile & Accessories',
            'watch' => 'Watch',
            'entertainment' => 'Entertainment'
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sku' => 'SKU (Stock Keeping Unit)',
            'name' => 'Name',
            'description' => 'Description',
            'price' => 'Price',
            'category' => 'Category',
            'image' => 'Image',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getImageUrl()
    {
        if ($this->image) {
            return Yii::getAlias('@static/products/' . $this->image);
        }

        return Yii::getAlias('@static/products/product.jpg');
    }

    public function beforeSave($insert)
    {
        $this->imageFile = UploadedFile::getInstance($this, 'imageFile');

        if ($this->imageFile) {
            if ($this->image) {
                Yii::$app->s3->delete('products/' . $this->image);
            }

            $extension   = $this->imageFile->extension;
            $data        = file_get_contents($this->imageFile->tempName);
            $this->image = date('ymdHi') . '_' . uniqid() . '.' . $extension;

            Yii::$app->s3->upload('products/' . $this->image, $data, null, [
                'params' => [
                    'CacheControl' => 'public, max-age=31536000',
                ]
            ]);
        }

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        return parent::afterSave($insert, $changedAttributes);
    }
}
