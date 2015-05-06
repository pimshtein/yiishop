<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "product_attributive".
 *
 * @property integer $id
 * @property integer $product_id
 * @property integer $attributive_id
 * @property string $description
 *
 * @property Product $product
 * @property Attributive $attributive
 */
class ProductAttributive extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product_attributive';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'attributive_id'], 'integer'],
            [['description'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Товар',
            'attributive_id' => 'Атрибут',
            'description' => 'Значение',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttributive()
    {
        return $this->hasOne(Attributive::className(), ['id' => 'attributive_id']);
    }
}
