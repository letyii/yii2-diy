<?php
use yii\bootstrap\Html;
?>

<div class="row">
    <div class="col-md-9 col-sm-9 col-xs-12">
        <div id="let_containers"></div>
        <button id="addContainer" class="btn btn-success col-md-12 col-sm-12 col-xs-12">+</button>
    </div>
    <div class="col-md-3 col-sm-3 col-xs-12">
        <div id='let_widgets'>
            
        </div>
    </div>
</div>
<?php
$this->registerJsFile('@web/vendor/bower/jquery-ui/jquery-ui.min', ['depends' => yii\web\JqueryAsset::className()]);
$this->registerJs("
    $('#addContainer').click(function(){
        var template = $('#containerTemplate').html();
        $('#let_containers').append(template);
    });
    
    function addPosition(element){
        var let_position = prompt('Please enter your number position', '1');
        var let_template = $('#positionTemplate').html().replace(/{numberPostion}/g, let_position);
        $(element).parents('.let_container').find('#let_positions').append(let_template);
        $('.let_position').sortable();
        $('.let_position').disableSelection();
    };
", yii\web\View::POS_END);

$this->registerJs("
    $('.let_position').sortable();
    $('.let_position').disableSelection();
", yii\web\View::POS_READY);
?>

<!-- TEMPLATE -->
<div id="containerTemplate" style="display: none;">
    <div class="let_container">
        <div class="panel panel-default">
            <div class="panel-heading">
                Container name
                <div class="pull-right"><?= Html::button('<i class="glyphicon glyphicon-plus"></i>', ['class' => 'btn btn-success btn-xs', 'onclick' => 'addPosition(this);']) ?></div>
            </div>
            <div class="panel-body" id="let_positions"></div>
        </div>
    </div>
</div>

<!-- Column Template -->
<div id="positionTemplate" style="display: none;">
    <div class="col-md-{numberPostion} col-sm-{numberPostion} col-xs-12 let_position">
        
    </div>
</div>
<style>
    .let_container {min-height: 40px; margin-bottom: 10px;}
    .let_position {border: 1px dashed #999; background: #f5f5f5; min-height: 20px;}
    #let_widgets {border: 1px dashed #999; min-height: 200px;}
</style>