<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$this->title = 'Построение графа';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="banks-addresses-index">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'csvFile')->fileInput()->label('Пожалуйста, загрузите CSV-файл, чтобы создать график.') ?>

    <div class="form-group">
        <?= Html::submitButton('Загрузить', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>

    <div class="row">
        <div class="col-md-12">
            <?= Yii::$app->controller->renderPartial('_profit_based_graph', ['chartData' => $chartData ?? null]) ?>
        </div>

        <div class="col-md-6">
            <?= Yii::$app->controller->renderPartial('_type_based_graph', ['balanceType' => $balanceType ?? null]) ?>
        </div>

        <div class="col-md-6">
            <?= Yii::$app->controller->renderPartial('_state_based_graph', ['profitState' => $profitState ?? null]) ?>
        </div>
    </div>
</div>

<script> // prevents form resubmission
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href)
    }
</script>