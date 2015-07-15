<?php
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use letyii\diy\models\Diy;
?>

<div class="row">
    <div class="col-md-9 col-sm-9 col-xs-12">
        <div id="let_containers">
            <?php if (is_array($model->data) AND !empty($model->data)): foreach ($model->data as $idContainer => $container): ?>
                <?= Diy::getTemplateContainer((string) $model->_id, $idContainer); ?>
            <?php endforeach; endif; ?>
        </div>
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
    // Add container from database
    $('#addContainer').click(function(){
        var template = '" . Diy::getTemplateContainer() . "';
        var idDiy = '" . Yii::$app->request->get('id') . "';
        var id = Math.random().toString(36).replace(/[^a-z|0-9]+/g, '').substr(0, 12);
        template = template.replace(/{idDiy}/g, idDiy).replace(/{id}/g, id);
        $.ajax({
            url: '" . Url::to(['/diy/ajax/addcontainer']) . "',
            type: 'POST',
            data: {idDiy: idDiy, id: id},
        })
        .done(function (data){
            if (data == 1)
                $('#let_containers').append(template);
            else
                alert('Có lỗi xảy ra! Không thể thêm mới container.');
        });
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
                console.log(draggable_id);
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
            data: {id: draggable_id},
        }).done(function(data){
            $(let_position).append(data);
        });
    }
", yii\web\View::POS_END);

$this->registerJs("
    setDropable();
    setDraggable();
", yii\web\View::POS_READY);
?>
<!-- Column Template -->
<div id="positionTemplate" style="display: none;">
    <div class="col-md-{numberPostion} col-sm-{numberPostion} col-xs-12 let_position"></div>
</div>

<!-- Widget template by row -->
<div id="widgetTemplate" style="display: none;">
    <div class="let_widget" data-id="{id}">
        <div class="btn btn-info">{title}</div>
        <?php
            Modal::begin([
                'header' => 'Setting widget',
                'toggleButton' => [
                    'label' => '<i class="glyphicon glyphicon-plus"></i>',
                    'class' => 'btn btn-success',
                ],
                'id' => 'modal_widget'
            ]);
        ?>
        <div class="row" id="settingWidget">
            
        </div>
        <?php Modal::end(); ?>
    </div>
</div>

<style>
    .let_container {min-height: 40px; margin-bottom: 10px;}
    .let_position {border: 1px dashed #999; background: #f5f5f5; min-height: 100px;}
    .let_widget {background: white; border: 1px solid #e7eaec; padding: 7px; margin-bottom: 10px;}
</style>