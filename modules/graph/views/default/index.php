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
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#menu1">Parsing by only positive value</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#menu2">Parsing by converting negative value to positive</a>
            </li>
         <!--   <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#menu3">Parsing by price column</a>
            </li>-->
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div class="tab-pane container active" id="home">
                <?= Yii::$app->controller->renderPartial('_profit_based_graph_any', ['chartData' => $chartData['any'] ?? null]) ?>
            </div>
            <div class="tab-pane container fade" id="menu1">
                 <?= Yii::$app->controller->renderPartial('_profit_based_graph_positive', ['chartData' => $chartData['positive'] ?? null]) ?>
            </div>
            <div class="tab-pane container fade" id="menu2">
                 <?= Yii::$app->controller->renderPartial('_profit_based_graph_transform', ['chartData' => $chartData['transform'] ?? null]) ?>
            </div>

        </div>
       <!-- <div class="col-md-12">
        </div>-->


    </div>
    <?php endif; ?>
   <!-- <div class="col-md-6">
        <?php /*= Yii::$app->controller->renderPartial('_type_based_graph', ['balanceType' => $balanceType ?? null]) */?>
    </div>

    <div class="col-md-6">
        <?php /*= Yii::$app->controller->renderPartial('_state_based_graph', ['profitState' => $profitState ?? null]) */?>
    </div>-->
</div>

<script> // prevents form resubmission
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href)
    }
</script>