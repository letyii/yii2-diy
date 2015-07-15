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
     * Ham add 1 container vao trong layout moi 1 container 
     * tuong ung voi 1 row trong layout
     */
    public function actionAddcontainer(){
        $result = 0;
        $idContainer = Yii::$app->request->post('id');
        $idDiy = Yii::$app->request->post('idDiy');
        $model = Diy::find()->where(['_id' => $idDiy])->one();
        if ($model) {
            if (empty($model->data)){
                $model->data = [
                    $idContainer => []
                ];
            } else {
                $model->data = ArrayHelper::merge($model->data, [
                    $idContainer => []
                ]);
            }
            
            if ($model->save())
                $result = 1;
            else
                $result = 0;
        }
        
        echo $result;
    }
}