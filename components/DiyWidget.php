<?php

namespace letyii\diy\components;

use Yii;
use yii\base\Widget;

class DiyWidget extends Widget {
    
    /**
     * @var string widget name
     */
    public $widgetName;
    
    /**
     * @var string Category name
     */
    public $diyCategory = '';
    
    /**
     * $diySetting = [
     *      'keyName' => [
     *          'type' => '', // text, textarea, editor, date, datetime, daterange, dropdown, checkbox, radio
     *          'value' => '',
     *          'items' => '', // Chi hien thi voi type = dropdown, checkbox, radio
     *      ],
     *      ...
     * ]
     * 
     * Moi row la mot input. Thuoc tinh cua input bao gom:
     * - type: input co the la text, textarea, editor, date, datetime, daterange, dropdown, checkbox, radio
     * - value: Gia tri cua input sau khi duoc luu
     * - items: La mang dung cho cac dang type: dropdown, checkbox, radio
     * @var array Bao gom cac rows khai bao cac inputs
     */
    public $diySetting = [];
}

