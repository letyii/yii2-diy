<?php
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use kartik\switchinput\SwitchInput;
?>
<?php
$form = ActiveForm::begin([
    'id' => 'formDefault',
    'options' => ['enctype' => 'multipart/form-data'],
]);
?>
<div class="btn-group">
<?php
echo Html::a('Create', ['/diy/default/create'], ['class' => 'btn btn-success', 'style' => 'margin: 20px 0;']);
echo Html::submitButton('Save', ['class' => 'btn btn-success', 'style' => 'margin: 20px 0;']);
?>
</div>
<div>
    <?php
        echo $form->field($model, 'title')->textInput();
        
        echo $form->field($model, 'status')->widget(SwitchInput::className([
            'type' => SwitchInput::RADIO,
        ]));

        ActiveForm::end(); 
    ?>
</div>