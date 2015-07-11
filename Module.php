<?php

namespace letyii\diy;

use Yii;

class Module extends \yii\base\Module
{
    
    public $permissionName = [
        'index' => '',
        'create' => '',
        'update' => '',
        'delete' => '',
        'build' => '',
    ];

    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
