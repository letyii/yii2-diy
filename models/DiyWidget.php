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
        $templateWidget = Html::beginTag('div', ['data-id' => (string) $model->_id, 'data-category' => $model->category, 'class' => 'let_widget']);
            $templateWidget .= $model->title;
        $templateWidget .= Html::endTag('div');
        
        return $templateWidget;
    }

    /**
     * Ham generate template widget khi move vao postion.
     * @param string $id id cua widget
     * @param array $values Mang gia tri cua cac option
     * @return string
     */
    public static function generateTemplateSetting($id, $values){
        // Get widget info by id
        $model = self::find()->where(['_id' => $id])->one();
        
        $templateSetting = null;
        if ($model){
            // Template widget
            $templateSetting = Html::beginTag('div', ['class' => 'let_widget', 'data-id' => $id]);
                $templateSetting .= Html::beginTag('div', ['class' => 'btn btn-info']);
                    $templateSetting .= $model->title;
                $templateSetting .= Html::endTag('div');
                $templateSetting .= Html::button('<i class="glyphicon glyphicon-plus"></i>', ['type' => 'button', 'class' => 'btn btn-success', 'data-toggle' => 'modal', 'data-target' => '#widget']);
                $templateSetting .= Html::beginTag('div', ['class' => 'modal fade bs-example-modal-lg', 'tabindex' => '-1', 'id' => 'widget', 'role' => 'dialog', 'aria-labelledby' => 'myLargeModalLabel']);
                    $templateSetting .= Html::beginTag('div', ['class' => 'modal-dialog modal-lg']);
                        $templateSetting .= Html::beginTag('div', ['class' => 'modal-content']);
                            $templateSetting .= Html::beginTag('div', ['class' => 'modal-header']);
                                $templateSetting .= Html::button('<span aria-hidden="true">&times;</span>', ['type' => 'button', 'class' => 'close', 'data-dismiss' => 'modal', 'aria-label' => 'Close']);
                                $templateSetting .= Html::beginTag('h4', ['class' => 'modal-title', 'id' => 'gridSystemModalLabel']);
                                    $templateSetting .= 'Modal title';
                                $templateSetting .= Html::endTag('h4');
                            $templateSetting .= Html::endTag('div');// End .modal-header
                            $templateSetting .= Html::beginTag('div', ['class' => 'modal-body']);
                                $templateSetting .= Html::beginTag('div', ['class' => 'row']);
                                    if (!empty($model->setting)) {
                                        foreach ($model->setting as $keySetting => $config) {
                                            // Kieu hien thi cua setting
                                            $type = ArrayHelper::getValue($config, 'type');
                                            // Gia tri cua setting
                                            $value = ArrayHelper::getValue($values, $keySetting);
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
                                $templateSetting .= Html::endTag('div');// End .row
                            $templateSetting .= Html::endTag('div');// End .modal-body
                        $templateSetting .= Html::endTag('div');// End .modal-content
                    $templateSetting .= Html::endTag('div');// End .modal-dialog
                $templateSetting .= Html::endTag('div');// End .modal
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
                $templateSetting = Html::textarea('DiyWidget[setting][' . $keySetting .']', $value, ['class' => 'form-control']);
                break;
            case 'date':
                $templateSetting = DateControl::widget([
                    'name' => 'DiyWidget[setting][' . $keySetting .']',
                    'type'=>DateControl::FORMAT_DATE,
                    'ajaxConversion'=>false,
                    'options' => [
                        'pluginOptions' => [
                            'autoclose' => true
                        ]
                    ]
                ]);
                break;
            case 'datetime':
                $templateSetting = DateControl::widget([
                    'name' => 'DiyWidget[setting][' . $keySetting .']',
                    'type'=>DateControl::FORMAT_DATETIME,
                    'ajaxConversion'=>false,
                    'options' => [
                        'pluginOptions' => [
                            'autoclose' => true
                        ]
                    ]
                ]);
                break;
            case 'daterange':
                $templateSetting = DateRangePicker::widget([
                    'name'=>'DiyWidget[setting][' . $keySetting .']',
                    'presetDropdown'=>true,
                    'hideInput'=>true
                ]);
                break;
            case 'dropdown':
                $templateSetting = Html::dropDownList('DiyWidget[setting][' . $keySetting .']', $value, $items, ['class' => 'form-control']);
                break;
            case 'checkbox':
                $templateSetting = Html::checkboxList('DiyWidget[setting][' . $keySetting .']', $value, $items, ['class' => 'form-control']);
                break;
            case 'radio':
                $templateSetting = Html::radioList('DiyWidget[setting][' . $keySetting .']', $value, $items, ['class' => 'form-control']);
                break;
            default:
                $templateSetting = Html::textInput('DiyWidget[setting][' . $keySetting .']', $value, ['class' => 'form-control']);
                break;
        }
        
        return $templateSetting;
    }
}