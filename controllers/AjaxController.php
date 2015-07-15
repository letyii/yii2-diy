<?php
namespace letyii\diy\controllers;

use Yii;
use yii\web\Controller;
use letyii\diy\models\Diy;
use yii\helpers\ArrayHelper;
use letyii\diy\models\DiyWidget;
use yii\bootstrap\Html;

class AjaxController extends Controller {
    /**
     * Ham add widget vao database
     */
    public function actionGetwidget(){
        $class = Yii::$app->request->post('class');
        if (class_exists($class)) {
            $class = new $class;
            if (is_subclass_of($class, 'letyii\diy\components\DiyWidget')) {
                $model = new DiyWidget;
                $model->title = $class->widgetName;
                $model->category = $class->diyCategory;
                $model->setting = $class->diySetting;
                if ($model->save()){
                    echo json_encode([
                        'widgetName' => $class->widgetName,
                        'diyCategory' => $class->diyCategory,
                        'diySetting' => $class->diySetting,
                    ]);
                }
            }
                
        } else {
            echo 'Khong phai class';
        }
    }
    
    /**
     * Get widget info by id from database
     */
    public function actionGetwidgetinfofromdb(){
        $id = Yii::$app->request->post('id');
        $values = [];
        echo $templateSetting = DiyWidget::getTemplateSetting($id, $values);
//        $model = DiyWidget::find()->where(['_id' => $id])->asArray()->one();
//        echo json_encode($model);
    }
    
    /**
     * Ham add 1 item vao trong layout
     */
    public function actionAdditem(){
        $diyId = Yii::$app->request->post('diyId');
        $type = Yii::$app->request->post('type');
        $containerId = Yii::$app->request->post('containerId');
        $numberColumn = Yii::$app->request->post('numberColumn');
        
        // Generate random item id by type
        $itemId = uniqid($type . '_');
        
        // Check type item
        switch ($type) {
            case Diy::Container:
                $template = $this->addContainer($diyId, $itemId);
                break;
            case Diy::Position:
                $template = $this->addPosition($diyId, $itemId, $containerId, $numberColumn);
                break;
        }
        
        echo $template;
    }
    
    /**
     * Ham generate template container va add container vao database
     * @param string $diyId id cua diy
     * @param string $itemId id cua container
     * @return string
     */
    private function addContainer($diyId, $itemId){
        // Generate template container
        $template = Diy::generateTemplateContainer($diyId, $itemId);
        
        $model = Diy::find()->where(['_id' => $diyId])->one();
        if ($model) {
            // Neu data la mang rong thi add container moi vao
            if (empty($model->data)){
                $model->data = [
                    $itemId => []
                ];
            } else { // Neu data khong phai la mang rong thi merge container moi vao mang hien co
                $model->data = ArrayHelper::merge($model->data, [
                    $itemId => []
                ]);
            }
            
            $model->save();
        }
        
        return $template;
    }
    
    private function addPosition($diyId, $itemId, $containerId, $numberColumn){
        // Generate template container
        $template = Diy::generateTemplatePosition($numberColumn, $diyId, $itemId);
        
        $model = Diy::find()->where(['_id' => $diyId])->one();
        if ($model) {
            // Check container co position hay chua
            $container = ArrayHelper::getValue($model->data, $containerId);
            // Neu chua thi add moi vao container
            if (empty($container)){
                $model->data = ArrayHelper::merge($model->data, [
                    $containerId => [
                        $itemId => ['column' => $numberColumn]
                    ]
                ]);
            } else { // Neu co position trong container thi add them vao mang hien co
                $model->data = ArrayHelper::merge($model->data, [
                        $containerId => ArrayHelper::merge($model->data[$containerId], [
                            $itemId => ['column' => $numberColumn]
                        ])
                    ]
                );
            }
            
            $model->save();
        }
        
        return $template;
    }
    
    public function actionSortitems(){
        $type = Yii::$app->request->post('type');
        $data = Yii::$app->request->post('data');
        $diyId = Yii::$app->request->post('diyId');
        $containerId = Yii::$app->request->post('containerId');
        $positionId = Yii::$app->request->post('positionId');
        
        $result = Diy::sortItems($type, $data, $diyId, $containerId, $positionId);
        
        if ($result)
            echo 1;
        else
            echo 0;
    }
}