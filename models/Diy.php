<?php

namespace letyii\diy\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\bootstrap\Html;

/**
 * This is the model class for collection "diy".
 *
 * @property \MongoId|string $_id
 * @property mixed $title
 * @property mixed $data = [
    // Moi row la mot container
    'row1' => [
        // Moi row la mot position
        'colum1' => [
            'column' => 12,
            'widgets' => [
                // Moi row la mot widget
                'widget_1' => [
                    'id' => 'ID_CUA_WIDGET',
                    'options' => [
                        'from_time' => '',
                        'to_time' => '',
                    ],
                ],
            ]
        ],
    ],
];
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
    
    /**
     * Ham get ra template cua container
     * @param type $idDiy id cua diy dang build layout
     * @param type $id id cua container duoc sinh ra
     * @return string
     */
    public static function getTemplateContainer($idDiy = '{idDiy}', $id = '{id}'){
        $templateContainer = '<div class="let_container" data-idDiy="' . $idDiy . '" data-id="' . $id . '">';
            $templateContainer .= '<div class="panel panel-default">';
                $templateContainer .= '<div class="panel-heading clearfix">';
                    $templateContainer .= '<div class="pull-right">';
                        $templateContainer .= Html::button('<i class="glyphicon glyphicon-plus"></i>', ['class' => 'btn btn-success btn-xs', 'onclick' => 'addPosition(this);']);
                    $templateContainer .= '</div>';
                $templateContainer .= '</div>';
                $templateContainer .= '<div class="panel-body" id="let_positions"></div>';
            $templateContainer .= '</div>';
        $templateContainer .= '</div>';
        return $templateContainer;
    }
}