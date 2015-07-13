<?php
namespace letyii\diy\controllers;

use Yii;
use yii\web\Controller;
use letyii\diy\models\Diy;
use yii\helpers\ArrayHelper;
use letyii\diy\models\DiyWidget;

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
        $model = DiyWidget::find()->where(['_id' => $id])->asArray()->one();
        echo json_encode($model);
    }
}