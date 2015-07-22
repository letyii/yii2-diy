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
     * Ham add widget vao database by namespace
     */
    public function actionAddwidget(){
        $class = Yii::$app->request->post('class');
        $message = [
            'template' => '',
            'status' => 0,
            'message' => '',
        ];
        if (class_exists($class)) {
            $class = new $class;
            if (is_subclass_of($class, 'letyii\diy\components\DiyWidget')) {
                $model = new DiyWidget;
                $model->title = $class->widgetName;
                $model->category = $class->diyCategory;
                $model->setting = $class->diySetting;
                if ($model->save()){
                    $message = [
                        'template' => DiyWidget::generateTemplateWidget($model),
                        'status' => 1,
                        'message' => 'Thêm mới thành công',
                    ];
                }
            } else {
                $message = [
                    'status' => 0,
                    'message' => 'Widget không được extend từ DiyWiget',
                ];
            }
                
        } else {
            $message = [
                'status' => 0,
                'message' => 'Widget không phải là class',
            ];
        }
        
        echo json_encode($message);
    }
    
    /**
     * Ham add 1 item vao trong layout
     */
    public function actionAdditem(){
        // Kieu item duoc sap xep
        $type = Yii::$app->request->post('type');
        
        // Id cua diy
        $diyId = Yii::$app->request->post('diyId');
        
        // Id cua container khi sap xep position, widget, them moi position
        $containerId = Yii::$app->request->post('containerId');
        
        // Id cua position khi sap xep, them moi widget
        $positionId = Yii::$app->request->post('positionId');
        
        // So cot cua mot position
        $numberColumn = Yii::$app->request->post('numberColumn');
        
        // Id cua widget
        $draggable_id = Yii::$app->request->post('draggable_id');
        
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
            case Diy::Widget:
                $template = $this->addWidget($diyId, $itemId, $containerId, $positionId, $draggable_id);
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
    
    /**
     * Ham add mot position vao container
     * @param string $diyId id cua diy
     * @param string $itemId id cua position
     * @param string $containerId container id cua position
     * @param int $numberColumn so cot cua position
     * @return string
     */
    private function addPosition($diyId, $itemId, $containerId, $numberColumn){
        // Generate template container
        $template = Diy::generateTemplatePosition($numberColumn, $diyId, $containerId, $itemId);
        
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
    
    /**
     * Ham them moi mot widget vao position
     * @param string $diyId id cua diy
     * @param string $itemId id cua widget duoc luu vao mang
     * @param string $containerId id cua container chua position
     * @param string $positionId id cua position duoc move vao
     * @param string $draggable_id id cua widget trong database
     * @return string
     */
    private function addWidget($diyId, $itemId, $containerId, $positionId, $draggable_id){
        // Generate template widget
        $template = DiyWidget::generateTemplateSetting($containerId, $positionId, $itemId, $draggable_id, []);
        
        $model = Diy::find()->where(['_id' => $diyId])->one();
        if ($model) {
            // Check position co widget hay chua
            $position = ArrayHelper::getValue($model->data[$containerId][$positionId], 'widgets', []);
            // Neu chua thi add moi vao position
            if (empty($position)){
                $model->data = ArrayHelper::merge($model->data, [
                    $containerId => [
                        $positionId => ArrayHelper::merge($model->data[$containerId][$positionId], [
                            'widgets' => [
                                $itemId => [
                                    'id' => $draggable_id
                                ]
                            ]
                        ])
                    ]
                ]);
            } else { // Neu co widget trong position thi add them vao mang hien co
                $model->data = ArrayHelper::merge($model->data, [
                    $containerId => [
                        $positionId => ArrayHelper::merge($model->data[$containerId][$positionId], [
                            'widgets' => ArrayHelper::merge($model->data[$containerId][$positionId]['widgets'], [
                                $itemId => [
                                    'id' => $draggable_id
                                ]
                            ])
                        ])
                    ]
                ]);
            }
            
            $model->save();
        }
        
        return $template;
    }
    
    // Action xu ly viec sap xep cac item: container, position, widget
    public function actionSortitems(){
        // Kieu item duoc sap xep
        $type = Yii::$app->request->post('type');
        
        // Mang du lieu da sap xep
        $data = Yii::$app->request->post('data');
        
        // Id cua diy
        $diyId = Yii::$app->request->post('diyId');
        
        // Id cua container khi sap xep position, widget, them moi position
        $containerId = Yii::$app->request->post('containerId');
        
        // Id cua position khi sap xep, them moi widget
        $positionId = Yii::$app->request->post('positionId');
        
        // Ham sap xep item, tra ve gia tri boolean
        $result = Diy::sortItems($type, $data, $diyId, $containerId, $positionId);
        
        echo $result;
    }
    
    // Action xu ly viec xoa cac item: container, position, widget
    public function actionDeleteitems(){
        // Kieu item xoa
        $type = Yii::$app->request->post('type');
        
        // Id cua diy
        $diyId = Yii::$app->request->post('diyId');
        
        // Id cua container
        $containerId = Yii::$app->request->post('containerId');
        
        // Id cua position
        $positionId = Yii::$app->request->post('positionId');
        
        // Id cua widget
        $widgetId = Yii::$app->request->post('widgetId');
        
        // Ham sap xep item, tra ve gia tri boolean
        $result = Diy::deleteItems($type, $diyId, $containerId, $positionId, $widgetId);
        
        echo $result;
    }
    
    public function actionSavesettingwidget(){
        // Id cua diy
        $diyId = Yii::$app->request->post('diyId');
        
        // Id container
        $containerId = Yii::$app->request->post('containerId');
        
        // Id position
        $positionId = Yii::$app->request->post('positionId');
        
        // Id widget
        $widgetId = Yii::$app->request->post('widgetId');
        
        // Mang gia tri setting duoc luu
        $settings = Yii::$app->request->post('settings');

        // Call function save setting by widget
        $result = DiyWidget::saveSettingWidget($diyId, $containerId, $positionId, $widgetId, $settings);
        
        echo $result;
    }
}