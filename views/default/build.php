<?php
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use letyii\diy\models\Diy;
use letyii\diy\models\DiyWidget;
use yii\helpers\ArrayHelper;
?>
<div class="wrapper wrapper-content animated fadeInRight">
<div class="row clearfix">
    <div class="col-md-9 col-sm-9 col-xs-12">
        <div id="let_containers">
            <?php if (is_array($model->data) AND !empty($model->data)): foreach ($model->data as $containerId => $container): 
                $positionItems = ArrayHelper::getValue($model->data, $containerId);
            ?>
                <?= Diy::generateTemplateContainer((string) $model->_id, $containerId, $positionItems); ?>
            <?php endforeach; endif; ?>
        </div>
        <?= Html::button('<i class="glyphicon glyphicon-plus"></i>', ['class' => 'btn btn-success col-md-12 col-sm-12 col-xs-12', 'id' => 'addContainer']) ?>
    </div>
    <div class="col-md-3 col-sm-3 col-xs-12">
        <div id='let_widgets'>
            <?php foreach ($diy_widget as $widget): ?>
                <?= DiyWidget::generateTemplateWidget($widget); ?>
            <?php endforeach; ?>
        </div>
        <!-- Begin add widget button -->
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
                <?= Html::button('Get widget', ['class' => 'btn btn-success col-md-12 col-sm-12 col-xs-12', 'id' => 'getWidget', 'onclick' => 'addWidget();']) ?>
            </div>
        </div>
        <?php Modal::end(); ?>
        <!-- End add widget button -->
    </div>
</div>
<?php
$this->registerJsFile('@web/vendor/bower/jquery-ui/jquery-ui.min.js', ['depends' => yii\web\JqueryAsset::className()]);
$this->registerJs("
    // Keo tha sap xep position
    function sortPosition(){
        $('.let_positions').sortable({
            connectWith: '.let_positions',
            update: function(event, ui){
                var diyId = '" . Yii::$app->request->get('id') . "';
                var data = $(this).sortable('toArray');
                var containerId = $(event.target).attr('data-id');
                $.ajax({
                    url: '" . Url::to(['/diy/ajax/sortitems']) . "',
                    type: 'POST',
                    dataType: 'json',
                    data: {type: '" . Diy::Position . "', data: data, containerId: containerId, diyId: diyId},
                }).done(function(data){
                });
            }
        });
    }
    
    // Keo tha sap xep widget
    function sortWidget(){
        $('.let_widget_position').sortable({
            connectWith: '.let_widget_position',
            update: function(event, ui){
                var diyId = '" . Yii::$app->request->get('id') . "';
                var data = $(this).sortable('toArray');
                var containerId = $(event.target).parents('.let_container').attr('data-id');
                var positionId = $(event.target).parents('.let_position').attr('data-id');
                $.ajax({
                    url: '" . Url::to(['/diy/ajax/sortitems']) . "',
                    type: 'POST',
                    dataType: 'json',
                    data: {type: '" . Diy::Widget . "', data: data, containerId: containerId, positionId: positionId, diyId: diyId},
                }).done(function(data){
                });
            }
        });
    }
    
    // Add container from database
    $('#addContainer').click(function(){
        var diyId = '" . Yii::$app->request->get('id') . "';
        $.ajax({
            url: '" . Url::to(['/diy/ajax/additem']) . "',
            type: 'POST',
            data: {diyId: diyId, type: 'c'},
        })
        .done(function (data){
            $('#let_containers').append(data);
            sortPosition();
            sortWidget();
        });
    });
    
    // Them 1 position vao container
    function addPosition(element){
        var let_position = prompt('Please enter your number position', '12');
        var containerId = $(element).parents('.let_container').attr('data-id');
        if (let_position) {
            var diyId = '" . Yii::$app->request->get('id') . "';
            $.ajax({
                url: '" . Url::to(['/diy/ajax/additem']) . "',
                type: 'POST',
                data: {diyId: diyId, type: 'p', containerId: containerId, numberColumn: let_position},
            })
            .done(function (data){
                $(element).parents('.let_container').find('.let_positions').append(data);
                setDraggable();
                setDropablePosition();
            });
        }
    };
    
    // Them 1 widget qua namespace vao database 
    function addWidget(){
        var let_addClass = $('#let_addClass').val();
        $.ajax({
            url: '" . Url::to(['/diy/ajax/addwidget']) . "',
            type: 'POST',
            dataType: 'json',
            data: {class: let_addClass},
        })
        .done(function (data){
            if (data.status == 1)
                $('#let_widgets').append(data.template);
            
            alert(data.message);
            setDraggable();
        });
    }
    
    // Them widget vao position duoc keo khi keo tha tu danh sach widget
    function setDropablePosition(){
        $('.let_position').droppable({
            accept: '.let_widget_origin',
            drop: function(event, ui) {
                var containerId = $('#' + $(event.target).attr('id')).parent().attr('data-id');
                var draggable_id = $(event.toElement).attr('data-id');
                var positionId = $(event.target).attr('id');
                getWidgetInfoFromDb(containerId, positionId, draggable_id, this);
                sortWidget();
            }
        });
    }
    
    // Tao hieu ung keo tha widget
    function setDraggable(){
        $('.let_widget_origin').draggable({
            connectWith: '.let_widget_origin',
            helper: 'clone',
            revert: 'invalid'
        });
    }
    
    function accordionWidget(widgetId){
        if ($('#setting_widget_' + widgetId).attr('data-show') == 0) {
            $('.setting_widget').hide();
            $('.setting_widget').attr('data-show', 0);
            $('#setting_widget_' + widgetId).show();
            $('#setting_widget_' + widgetId).attr('data-show', 1);
        } else {
            $('#setting_widget_' + widgetId).hide();
            $('.setting_widget').attr('data-show', 0);
            $('#setting_widget_' + widgetId).attr('data-show', 0);
        }
    }
    
    // Get widget info by id from database
    function getWidgetInfoFromDb(containerId, positionId, draggable_id, let_position){
        var diyId = '" . Yii::$app->request->get('id') . "';
        $.ajax({
            url: '" . Url::to(['/diy/ajax/additem']) . "',
            type: 'POST',
            data: {diyId: diyId, type: 'w', containerId: containerId, positionId: positionId, draggable_id: draggable_id},
        }).done(function(data){
            $(let_position).find('.let_widget_position').append(data);
        });
    }
    
    // Call action save setting widget
    function saveSettingsWidget(element){
        var diyId = '" . Yii::$app->request->get('id') . "';
        var settings = $(element).parents('#settingForm').serializeArray();
        var containerId = $(element).parents('.setting_widget').attr('data-container');
        var positionId = $(element).parents('.setting_widget').attr('data-position');
        var widgetId = $(element).parents('.setting_widget').attr('data-id');
        $.ajax({
            url: '" . Url::to(['/diy/ajax/savesettingwidget']) . "',
            type: 'POST',
            data: {diyId: diyId, containerId: containerId, positionId: positionId, widgetId: widgetId, settings: settings},
        }).done(function(data){
        });
    }
    
    // Call action delete item
    function deleteItems(element, type, itemRemove){
        var diyId = '" . Yii::$app->request->get('id') . "';
        var containerId = $(element).parents('.let_container').attr('id');
        var positionId = $(element).parents('.let_position').attr('id');
        var widgetId = $(element).parents('.let_widget').attr('id');
        var confirmAlert = confirm('" . Yii::t('yii', 'Are you sure you want to delete this item?') . "');
        if (confirmAlert == true){
            $.ajax({
                url: '" . Url::to(['/diy/ajax/deleteitems']) . "',
                type: 'POST',
                data: {diyId: diyId, containerId: containerId, positionId: positionId, widgetId: widgetId, type: type},
            }).done(function(status){
                if (status == 1)
                    $(element).parents(itemRemove).remove();
                else 
                    alert('Có lỗi xảy ra!');
            });
        } else {
            alert(0);
        }
    }
", yii\web\View::POS_END);

$this->registerJs("
    setDropablePosition();
    setDraggable();
    // Keo tha sap xep container
    $('#let_containers').sortable({
        handle: '.panel-heading',
        cancel: '.let_positions',
        update: function(event, ui){
            var data = $(this).sortable('toArray');
            var diyId = '" . Yii::$app->request->get('id') . "';
            $.ajax({
                url: '" . Url::to(['/diy/ajax/sortitems']) . "',
                type: 'POST',
                data: {type: '" . Diy::Container . "', data: data, diyId: diyId},
            }).done(function(data){
            });
        }
    });
    
    sortPosition();
    sortWidget();
", yii\web\View::POS_READY);
?>
</div>
<style>
    .let_container {min-height: 40px; margin-bottom: 10px;}
    .let_position {border: 1px solid #999; min-height: 100px;}
    .buttonDelete, .buttonDeleteWidget {display: none;}
    .let_position:hover .buttonDelete{display: block}
    .let_widget:hover .buttonDeleteWidget{display: block}
    .let_widget {background: white; border: 1px solid #e7eaec; padding: 7px; margin: 10px 0;}
    .let_widget_origin {background: white; border: 1px solid #e7eaec; padding: 7px; margin-bottom: 10px;}
</style>