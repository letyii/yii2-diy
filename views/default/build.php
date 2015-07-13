<?php
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;
?>

<div class="row">
    <div class="col-md-9 col-sm-9 col-xs-12">
        <div id="let_containers"></div>
        <?= Html::button('<i class="glyphicon glyphicon-plus"></i>', ['class' => 'btn btn-success col-md-12 col-sm-12 col-xs-12', 'id' => 'addContainer']) ?>
    </div>
    <div class="col-md-3 col-sm-3 col-xs-12">
        <div id='let_widgets'>
            <?php foreach ($diy_widget as $widget): ?>
                <div data-id="<?= (string) $widget->_id; ?>" data-category="<?= $widget->category; ?>" class="let_widget"><?= $widget->title; ?></div>
            <?php endforeach; ?>
        </div>
        <?php
            Modal::begin([
                'header' => 'Load widget by namespace',
                'toggleButton' => [
                    'label' => '<i class="glyphicon glyphicon-plus"></i>',
                    'class' => 'btn btn-success col-md-12 col-sm-12 col-xs-12',
                ],
                'id' => 'modal_widget'
            ]);
        ?>
        <div class="row">
            <div class="col-md-9 col-sm-9 col-xs-12">
                <input type="text" id="let_addClass" class="form-control" />
            </div>
            <div class="col-md-3 col-sm-3 col-xs-12">
                <?= Html::button('Get widget', ['class' => 'btn btn-success col-md-12 col-sm-12 col-xs-12', 'id' => 'getWidget', 'onclick' => 'getWidget();']) ?>
            </div>
        </div>
        <?php Modal::end(); ?>
    </div>
</div>
<?php
$this->registerJsFile('@web/vendor/bower/jquery-ui/jquery-ui.min', ['depends' => yii\web\JqueryAsset::className()]);
$this->registerJs("
    $('#addContainer').click(function(){
        var template = $('#containerTemplate').html();
        $('#let_containers').append(template);
    });
    
    // Them 1 position vao container
    function addPosition(element){
        var let_position = prompt('Please enter your number position', '1');
        var let_template = $('#positionTemplate').html().replace(/{numberPostion}/g, let_position);
        $(element).parents('.let_container').find('#let_positions').append(let_template);
        setDropable();
    };
    
    // Get data widget by namespace
    function getWidget(){
        var let_addClass = $('#let_addClass').val();
        $.ajax({
            url: '" . Url::to(['/diy/ajax/getwidget']) . "',
            type: 'POST',
            dataType: 'json',
            data: {class: let_addClass},
        })
        .done(function (data){
            
        });
    }
    
    // Set position to droppable
    function setDropable(){
        $('.let_position').droppable({
            drop: function(event, ui) {
                var draggable_id = $(event.toElement).attr('data-id');
                getWidgetInfoFromDb(draggable_id, this);
            }
        });
    }
    
    function setDraggable(){
        $('.let_widget').draggable({
            connectWith: '.let_widget',
            helper: 'clone',
            revert: 'invalid'
        });
    }
    
    // Get widget info by id from database
    function getWidgetInfoFromDb(draggable_id, let_position){
        $.ajax({
            url: '" . Url::to(['/diy/ajax/getwidgetinfofromdb']) . "',
            type: 'POST',
            dataType: 'json',
            data: {id: draggable_id},
        }).done(function(data){
            console.log(data);
            var widgetTemplate = $('#widgetTemplate').html().replace(/{title}/g, data.title);
            $(let_position).html(widgetTemplate);
            setDraggable();
        });
    }
", yii\web\View::POS_END);

$this->registerJs("
    setDropable();
    setDraggable();
", yii\web\View::POS_READY);
?>

<!-- TEMPLATE -->
<div id="containerTemplate" style="display: none;">
    <div class="let_container">
        <div class="panel panel-default">
            <div class="panel-heading clearfix">
                <div class="pull-right"><?= Html::button('<i class="glyphicon glyphicon-plus"></i>', ['class' => 'btn btn-success btn-xs', 'onclick' => 'addPosition(this);']) ?></div>
            </div>
            <div class="panel-body" id="let_positions"></div>
        </div>
    </div>
</div>

<!-- Column Template -->
<div id="positionTemplate" style="display: none;">
    <div class="col-md-{numberPostion} col-sm-{numberPostion} col-xs-12 let_position"></div>
</div>

<!-- Widget template by row -->
<div id="widgetTemplate" style="display: none;">
    <div class="let_widget">
        <div class="btn btn-info">{title}</div>
        <?= Html::button('<i class="glyphicon glyphicon-plus"></i>', ['class' => 'btn btn-success']) ?>
    </div>
</div>

<style>
    .let_container {min-height: 40px; margin-bottom: 10px;}
    .let_position {border: 1px dashed #999; background: #f5f5f5; min-height: 100px;}
    .let_widget {background: white; border: 1px solid #e7eaec; padding: 7px; margin-bottom: 10px;}
</style>