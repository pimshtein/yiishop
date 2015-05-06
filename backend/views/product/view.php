<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Product */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Товары', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'title',
            'description:ntext',

            [
                'label' => 'Категория',
                'value' => ArrayHelper::getValue($model, function ($model) {
                    return empty($model->category_id) ? '-' : $model->category->title;
                }),
            ],

            [
                'label' => 'Производитель',
                'value' => ArrayHelper::getValue($model, function ($model) {
                    return empty($model->brand_id) ? '-' : $model->brand->title;
                }),
            ],

            'price',
        ],
    ]) ?>

</div>
