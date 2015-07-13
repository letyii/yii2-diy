<?php

namespace letyii\diy\models;

use Yii;

/**
 * This is the model class for collection "diy_widget".
 *
 * @property \MongoId|string $_id
 * @property mixed $title
 * @property mixed $category
 * @property mixed $setting
 */
class BaseDiyWidget extends \yii\mongodb\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'diy_widget';
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'title',
            'category',
            'setting',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'category', 'setting'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('diy', 'ID'),
            'title' => Yii::t('diy', 'Title'),
            'category' => Yii::t('diy', 'Category'),
            'setting' => Yii::t('diy', 'Setting'),
        ];
    }
}