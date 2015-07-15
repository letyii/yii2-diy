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
    public static function getTemplateSetting($id, $values){
        // Get widget info by id
        $model = self::find()->where(['_id' => $id])->one();
        
        $templateSetting = null;
        if ($model){
            // Template widget
            $templateSetting = '<div class="let_widget" data-id="' . $id . '">';
                $templateSetting .= '<div class="btn btn-info">' . $model->title . '</div>';
                $templateSetting .= '<button type="button" class="btn btn-success" data-toggle="modal" data-target="#widget"><i class="glyphicon glyphicon-plus"></i></button>';
                $templateSetting .= '<!-- Begin modal --><div class="modal fade bs-example-modal-lg" tabindex="-1" id="widget" role="dialog" aria-labelledby="myLargeModalLabel">';
                    $templateSetting .= '<div class="modal-dialog modal-lg">';
                        $templateSetting .= '<div class="modal-content">';
                            $templateSetting .= '<div class="modal-header">';
                                $templateSetting .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                                $templateSetting .= '<h4 class="modal-title" id="gridSystemModalLabel">Modal title</h4>';
                            $templateSetting .= '</div><!-- End .modal-header -->';
                            $templateSetting .= '<div class="modal-body">';
                                $templateSetting .= '<div class="row">';
                                    if (!empty($model->setting)) {
                                        foreach ($model->setting as $keySetting => $config) {
                                            // Kieu hien thi cua setting
                                            $type = ArrayHelper::getValue($config, 'type');
                                            // Gia tri cua setting
                                            $value = ArrayHelper::getValue($values, $keySetting);
                                            // Danh sach cac gia tri cua setting neu la dropdown, checkbox, radio
                                            $items = ArrayHelper::getValue($config, 'items');

                                            $templateSetting .= '<div class="form-group field-setting-key">';
                                                $templateSetting .= '<label class="control-label col-sm-2" for="DiyWidget-' . $keySetting . '">' . $keySetting . '</label>';
                                                $templateSetting .= '<div class="col-sm-10">';
                                                    $templateSetting .= self::getInputByType($type, $templateSetting, $keySetting, $value, $items);
                                                    $templateSetting .= '<div class="help-block help-block-error help-block m-b-none"></div>';
                                                $templateSetting .= '</div>';
                                            $templateSetting .= '</div>';
                                        }
                                    }
                                $templateSetting .= '</div><!-- End .row -->';
                            $templateSetting .= '</div><!-- End .modal-body -->';
                        $templateSetting .= '</div><!-- End .modal-content -->';
                    $templateSetting .= '</div><!-- End .modal-dialog -->';
                $templateSetting .= '</div><!-- End modal -->';
            $templateSetting .= '</div>';
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