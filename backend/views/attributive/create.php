<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Attributive */

$this->title = 'Создать атрибут';
$this->params['breadcrumbs'][] = ['label' => 'Атрибуты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attributive-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
