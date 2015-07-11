<?php

namespace letyii\diy\models;

use Yii;
use yii\data\ActiveDataProvider;

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
class Diy extends BaseDiy
{
    public function search($params, $pageSize = 20)
    {
        $query = self::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
        ]);
        
        if (!($this->load($params) AND $this->validate())) {
            return $dataProvider;
        }
        
        $query = \app\helpers\LetHelper::addFilter($query, '_id', $this->_id, 'like');
        $query = \app\helpers\LetHelper::addFilter($query, 'title', $this->title, 'like');
        $query = \app\helpers\LetHelper::addFilter($query, 'creator', $this->creator);
        $query = \app\helpers\LetHelper::addFilter($query, 'status', $this->status);
        
        if (!empty($this->create_time)){
            list($minDate, $maxDate) = explode(' to ', $this->create_time);
            $min_date = new \MongoDate(strtotime($minDate . ' 00:00:00'));
            $max_date = new \MongoDate(strtotime($maxDate . ' 23:59:59'));
            $query = \app\helpers\LetHelper::addFilter($query, 'create_time', [$min_date, $max_date], 'between');
        }

        if (\app\helpers\ArrayHelper::getValue($params, 'sort') == NULL)
            $query->orderBy('create_time DESC');
        
        return $dataProvider;
    }
}