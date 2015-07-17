<?php

namespace letyii\diy\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for collection "diy".
 *
 * @property \MongoId|string $_id
 * @property mixed $title
 * @property mixed $data = [
    // Moi row la mot container
    'container_1' => [
        // Moi row la mot position
        'position_1' => [
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
    const Container = 'c';

    const Position = 'p';

    const Widget = 'w';

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
     * @param string $diyId id cua diy dang build layout
     * @param string $itemId id cua container duoc sinh ra
     * @param array $positionItems 1 mang cac position cua container
     * @return string
     */
    public static function generateTemplateContainer($diyId = null, $itemId = null, $positionItems = []){
        $templateContainer = Html::beginTag('div', ['class' => 'let_container', 'id' => $itemId, 'data-diyId' => $diyId, 'data-id' => $itemId]);
            $templateContainer .= Html::beginTag('div', ['class' => 'panel panel-default']);
                $templateContainer .= Html::beginTag('div', ['class' => 'panel-heading clearfix']);
                    $templateContainer .= Html::beginTag('div', ['class' => 'pull-right']);
                        $templateContainer .= Html::button('<i class="glyphicon glyphicon-plus"></i>', ['class' => 'btn btn-success btn-xs', 'onclick' => 'addPosition(this);']);
                    $templateContainer .= Html::endTag('div');
                $templateContainer .= Html::endTag('div');
                $templateContainer .= Html::beginTag('div', ['class' => 'panel-body let_positions', 'data-id' => $itemId]);
                    if (!empty($positionItems)) {
                        foreach ($positionItems as $positionId => $position) {
                            $column = ArrayHelper::getValue($position, 'column');
                            $widgetItems = ArrayHelper::getValue($position, 'widgets');
                            $templateContainer .= Diy::generateTemplatePosition($column, $diyId, $positionId, $widgetItems);
                        }
                    }
                $templateContainer .= Html::endTag('div');
            $templateContainer .= Html::endTag('div');
        $templateContainer .= Html::endTag('div');
        return $templateContainer;
    }
    
    /**
     * Ham get ra template cua position
     * @param int $numberColumn so cot cua position
     * @param string $diyId id cua diy
     * @param string $itemId id cua position
     * @param array $widgetItems mang cac widget cua position
     * @return string
     */
    public static function generateTemplatePosition($numberColumn = 12, $diyId = null, $itemId = null, $widgetItems = []){
        $tempalatePosition = Html::beginTag('div', ['class' => 'let_position col-md-' . $numberColumn . ' col-sm-' . $numberColumn . ' col-xs-12', 'id' => $itemId, 'data-diyId' => $diyId, 'data-id' => $itemId]);
            $tempalatePosition .= $itemId;
            if (!empty($widgetItems)) {
                foreach ($widgetItems as $widget) {
                    $id = ArrayHelper::getValue($widget, 'id');
                    $tempalatePosition .= DiyWidget::generateTemplateSetting($id, []);
                }
            }
        $tempalatePosition .= Html::endTag('div');
        return $tempalatePosition;
    }


    /**
     * Ham sap xep cac item va luu vao database.
     * @param string $type kieu cua item. La mot trong cac gia tri: self::Container, self::Position, self::Widget
     * @param array $data mang moi sap xep lai cac item
     * @param string $diyId id cua 1 row trong diy table
     * @param string $containerId id cua 1 container
     * @param string $positionId id cua 1 position
     * @return boolean
     */
    public static function sortItems($type, $data, $diyId, $containerId = null, $positionId = null){
        // Neu type khong la mot trong cac gia tri cho phep va $data rong, id cua diy rong thi return false
        if (!in_array($type, [self::Container, self::Position, self::Widget]) AND empty($data) AND empty($diyId))
            return false;
        
        $dataResult = [];
        
        // Get layout diy 
        $model = self::find()->where(['_id' => $diyId])->one();
        if ($model){
            $diy = $model->data;
            if ($type == self::Container){
                foreach ($data as $item) {
                    // Get value cua container gan vao mang moi
                    $dataResult[$item] = ArrayHelper::getValue($model->data, $item, []);
                }
                $model->data = $dataResult;
                return $model->save();
            } elseif ($type == self::Position){
                // Call function sort position
                self::sortPosition($data, $diy, $model, $containerId);
            }
        }
        
        return false;
    }
    
    /**
     * Ham sap xep position.
     * @param array $data mang position da duoc sap xep
     * @param array $diy mang $data cua layout diy
     * @param object $model layout diy
     * @param string $containerId container id
     * @return boolean
     */
    private static function sortPosition($data, $diy, $model, $containerId){
        // Danh sach cac position goc cua $containerTo
        $containerTo = ArrayHelper::getValue($diy, $containerId);
        // Truong hop move tu container nay sang container khac
        if (count($data) > count($containerTo)){
            // Check tung phan tu cua mang $data xem co ton tai trong $containerTo, phan tu khong thuoc $containerTo la phan tu moi duoc them vao container
            foreach ($data as $positionId) {
                if (!isset($containerTo[$positionId]))
                    $positionMoveId = $positionId;
            }

            // Xoa position duoc move ra khoi mang $containerFrom
            foreach ($diy as $key => $container) {
                if (isset($positionMoveId) AND isset($diy[$key][$positionMoveId]) AND $key !== $containerId) {
                    $positionMove = $diy[$key][$positionMoveId];
                    unset($diy[$key][$positionMoveId]);
                }
            }

            // Them moi position duoc move vao mang goc cua container
            if (isset($positionMove)) {
                $diy[$containerId][$positionMoveId] = $positionMove;
            }

            // Sap xep cac position theo dung thu tu duoc move
            foreach ($data as $positionId) {
                $dataResult[$positionId] = ArrayHelper::getValue($diy[$containerId], $positionId, []);
            }
            $diy[$containerId] = $dataResult;

            $model->data = $diy;
            return $model->save();
        } elseif (count($data) === count($containerTo)){ // Truong hop move cac position ben trong container
            // Sap xep cac position theo dung thu tu duoc move
            foreach ($data as $positionId) {
                $dataResult[$positionId] = ArrayHelper::getValue($diy[$containerId], $positionId, []);
            }
            $diy[$containerId] = $dataResult;

            $model->data = $diy;
            return $model->save();
        }
    }
}