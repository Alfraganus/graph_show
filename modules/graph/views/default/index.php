<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $data app\models\BanksAddresses[] */

$this->title = 'Адреса банков';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="banks-addresses-index">

    <h1><?= Html::encode($this->title) ?></h1>


    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'csvFile')->fileInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Загрузить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

