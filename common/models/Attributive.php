<?php

namespace common\models;

use Yii;
use yii\behaviors\SluggableBehavior;

/**
 * This is the model class for table "attributive".
 *
 * @property integer $id
 * @property string $title
 * @property string $slug
 * @property string $description
 *
 * @property ProductAttributive[] $productAttributives
 */
class Attributive extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'attributive';
    }

    public function behaviors()
    {
        return [
            [
                'class' => SluggableBehavior::className(),
                'attribute' => 'title',
                'slugAttribute' => 'slug',
                'transliterator' => 'Russian-Latin/BGN; NFKD',
                'forceUpdate' => false,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'slug', 'description'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название атрибута',
            'slug' => 'Seo ЧПУ',
            'description' => 'Описание',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductAttributives()
    {
        return $this->hasMany(ProductAttributive::className(), ['attributive_id' => 'id']);
    }
}
