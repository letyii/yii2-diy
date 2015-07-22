<?php

namespace letyii\diy\models;

use Yii;
use yii\bootstrap\Modal;
use yii\bootstrap\Html;
use kartik\datecontrol\DateControl;
use yii\helpers\ArrayHelper;
use kartik\daterange\DateRangePicker;

/**
 * This is the model class for collection "diy_widget".
 *
 * @property \MongoId|string $_id
 * @property mixed $title
 * @property mixed $category
 * @property mixed $setting
 */
class DiyWidget extends BaseDiyWidget
{
    /**
     * Ham hien thi widget khi duoc tao qua namespace
     * @param object $model thong tin cua widget khi duoc tao
     * @return string
     */
    public static function generateTemplateWidget($model){
        $templateWidget = Html::beginTag('div', ['data-id' => (string) $model->_id, 'data-category' => $model->category, 'class' => 'let_widget_origin']);
            $templateWidget .= $model->title;
        $templateWidget .= Html::endTag('div');
        
        return $templateWidget;
    }

    /**
     * Ham generate template widget khi move vao postion.
     * @param string $containerId id cua container
     * @param string $positionId id cua position
     * @param string $widgetId id cua widget trong mang data cua diy
     * @param string $id id cua widget
     * @param array $settings Mang gia tri cua cac option
     * @return string
     */
    public static function generateTemplateSetting($containerId, $positionId, $widgetId, $id, $settings){
        // Get widget info by id
        $model = self::find()->where(['_id' => $id])->one();
        
        $templateSetting = null;
        if ($model){
            // Template widget
            $templateSetting .= Html::beginTag('div', ['class' => 'let_widget row', 'data-id' => $id, 'id' => $widgetId]);
                $templateSetting .= Html::beginTag('div', ['class' => 'btn btn-info']);
                    $templateSetting .= $model->title;
                $templateSetting .= Html::endTag('div');
                // Begin button widget
                $templateSetting .= Html::beginTag('div', ['class' => 'pull-right']);
                    $templateSetting .= Html::beginTag('div', ['class' => 'btn-group buttonDeleteWidget']);
                        $templateSetting .= Html::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'button', 'class' => 'btn btn-danger btn-xs', 'onclick' => 'deleteItems(this, "w", ".let_widget");']);
                        $templateSetting .= Html::button('<i class="glyphicon glyphicon-plus"></i>', ['type' => 'button', 'class' => 'btn btn-success btn-xs', 'onclick' => 'accordionWidget("' . $widgetId . '");']);
                    $templateSetting .= Html::endTag('div');
                $templateSetting .= Html::endTag('div');
                // End button widget
                $templateSetting .= Html::beginTag('div', ['class' => 'row setting_widget', 'id' => 'setting_widget_' . $widgetId, 'style' => 'margin-top: 20px; display: none;', 'data-show' => 0, 'data-container' => $containerId, 'data-position' => $positionId, 'data-id' => $widgetId]);
                    // Begin template setting.
                    $templateSetting .= Html::beginForm(NULL, 'POST', ['role' => 'form', 'id' => 'settingForm']);
                        if (!empty($model->setting)) {
                            foreach ($model->setting as $keySetting => $config) {
                                // Kieu hien thi cua setting
                                $type = ArrayHelper::getValue($config, 'type');
                                // Gia tri cua setting
                                $value = ArrayHelper::getValue($settings, $keySetting);
                                // Danh sach cac gia tri cua setting neu la dropdown, checkbox, radio
                                $items = ArrayHelper::getValue($config, 'items');

                                $templateSetting .= Html::beginTag('div', ['class' => 'form-group field-setting-key']);
                                    $templateSetting .= Html::beginTag('label', ['class' => 'control-label col-sm-2', 'for' => 'DiyWidget-' . $keySetting . '']);
                                        $templateSetting .= $keySetting;
                                    $templateSetting .= Html::endTag('label');
                                    $templateSetting .= Html::beginTag('div', ['class' => 'col-sm-10']);
                                        $templateSetting .= self::getInputByType($type, $templateSetting, $keySetting, $value, $items);
                                        $templateSetting .= Html::beginTag('div', ['class' => 'help-block help-block-error help-block m-b-none']) . Html::endTag('div');
                                    $templateSetting .= Html::endTag('div');// End .col-sm-10
                                $templateSetting .= Html::endTag('div');// End .field-setting-key
                            }
                        }
                        // Begin button save
                        $templateSetting .= Html::beginTag('div', ['class' => 'col-sm-12']);
                            $templateSetting .= Html::beginTag('div', ['class' => 'pull-right']);
                                $templateSetting .= Html::button(Yii::t('diy', 'Save'), ['type' => 'button', 'class' => 'btn btn-success', 'onclick' => 'saveSettingsWidget(this);']);
                            $templateSetting .= Html::endTag('div');
                        $templateSetting .= Html::endTag('div');
                        // End button save
                    $templateSetting .= Html::endForm();
                    // End template setting
                $templateSetting .= Html::endTag('div');// End .row
            $templateSetting .= Html::endTag('div');// End .let_widget
        }
        
        return $templateSetting;
    }
    
    /**
     * Ham get html cho input theo type
     * @param string $type input co the la text, textarea, editor, date, datetime, daterange, dropdown, checkbox, radio
     * @param string $templateSetting giao dien input theo type
     * @param string $keySetting ten cua key setting
     * @param string $value gia tri cua key setting
     * @param array $items Mang cac gia tri cua setting neu setting co type la dropdown, checkbox, radio
     * @return string
     */
    private static function getInputByType($type = 'text', $templateSetting = null, $keySetting = null, $value = null, $items = []){
        switch ($type) {
            case 'textarea':
                $templateSetting = Html::textarea($keySetting, $value, ['class' => 'form-control', 'title' => $keySetting]);
                break;
            case 'date':
                $templateSetting = DateControl::widget([
                    'name' => $keySetting,
                    'value' => $value,
                    'type'=>DateControl::FORMAT_DATE,
                    'ajaxConversion'=>false,
                    'options' => [
                        'pluginOptions' => [
                            'autoclose' => true
                        ],
                        'options' => ['title' => $keySetting],
                    ],
                    'displayFormat' => 'dd-MM-yyyy',
                    'saveFormat' => 'yyyy-MM-dd',
                ]);
                break;
            case 'datetime':
                $templateSetting = DateControl::widget([
                    'name' => $keySetting,
                    'value' => $value,
                    'type'=>DateControl::FORMAT_DATETIME,
                    'ajaxConversion'=>false,
                    'options' => [
                        'pluginOptions' => [
                            'autoclose' => true
                        ],
                        'options' => ['title' => $keySetting],
                    ],
                    'saveFormat' => 'yyyy-MM-dd',
                ]);
                break;
            case 'daterange':
                $templateSetting = DateRangePicker::widget([
                    'name' => $keySetting,
                    'value' => $value,
                    'presetDropdown'=>true,
                    'hideInput'=>true,
                    'options' => ['title' => $keySetting],
                ]);
                break;
            case 'dropdown':
                $templateSetting = Html::dropDownList($keySetting, $value, $items, ['class' => 'form-control', 'title' => $keySetting]);
                break;
            case 'checkbox':
                $templateSetting = Html::checkboxList($keySetting, $value, $items, ['class' => 'form-control', 'title' => $keySetting]);
                break;
            case 'radio':
                $templateSetting = Html::radioList($keySetting, $value, $items, ['class' => 'form-control', 'title' => $keySetting]);
                break;
            default:
                $templateSetting = Html::textInput($keySetting, $value, ['class' => 'form-control', 'title' => $keySetting]);
                break;
        }
        
        return $templateSetting;
    }
    
    /**
     * Ham them/sua setting cua widget
     * @param string $diyId id cua diy
     * @param string $containerId id cua container
     * @param string $positionId id cua position
     * @param string $widgetId id cua widget
     * @param array $settings mang gia tri cua option
     * @return boolean
     */
    public function saveSettingWidget($diyId, $containerId, $positionId, $widgetId, $settings){
        // Map name setting with value
        $settingArray = ArrayHelper::map($settings, 'name', 'value');
        
        // Get widget info by id
        $model = Diy::find()->where(['_id' => $diyId])->one();
        
        if ($model) {
            $model->data = ArrayHelper::merge($model->data, [
                $containerId => [
                    $positionId => ArrayHelper::merge($model->data[$containerId][$positionId], [
                        'widgets' => ArrayHelper::merge($model->data[$containerId][$positionId]['widgets'], [
                            $widgetId => ArrayHelper::merge($model->data[$containerId][$positionId]['widgets'][$widgetId], [
                                'settings' => $settingArray
                            ])
                        ])
                    ])
                ]
            ]);
            
            return $model->save();
        }
        
        return false;
    }
}