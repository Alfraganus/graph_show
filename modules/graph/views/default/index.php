<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$this->title = 'Построение графа';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="banks-addresses-index">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'file')->fileInput()->label('Пожалуйста, загрузите файл CSV или HTML, чтобы создать график!') ?>
    <?= $form->field($model, 'show_interval')->checkbox()->label('Показать 20 в равных интервалах') ?>
    <div class="form-group">
        <?= Html::submitButton('Загрузить', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>

    <?php if(isset($chartData)) : ?>
    <div class="row">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#home">Parsing by each value</a>
            </li>

        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div class="tab-pane container active" id="home">
                <?= Yii::$app->controller->renderPartial('_profit_based_graph_any', ['chartData' => $chartData['any'] ?? null]) ?>
            </div>

        </div>
       <!-- <div class="col-md-12">
        </div>-->

    </div>
    <?php endif; ?>

</div>

<script> // prevents form resubmission
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href)
    }
</script>