<?php

namespace letyii\diy\models;

use Yii;

/**
 * This is the model class for collection "diy".
 *
 * @property \MongoId|string $_id
 * @property mixed $title
 * @property mixed $data
 * @property mixed $creator
 * @property mixed $create_time
 * @property mixed $editor
 * @property mixed $update_time
 * @property mixed $status
 */
class BaseDiy extends \yii\mongodb\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'diy';
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'title',
            'data',
            'creator',
            'create_time',
            'editor',
            'update_time',
            'status',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'data', 'creator', 'create_time', 'editor', 'update_time', 'status'], 'safe']
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
            'data' => Yii::t('diy', 'Data'),
            'creator' => Yii::t('diy', 'Creator'),
            'create_time' => Yii::t('diy', 'Create Time'),
            'editor' => Yii::t('diy', 'Editor'),
            'update_time' => Yii::t('diy', 'Update Time'),
            'status' => Yii::t('diy', 'Status'),
        ];
    }
}