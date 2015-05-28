<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AttributiveSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Атрибуты';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJs('
    $("#delete-button").on("click",function(e){
    e.preventDefault();
    alert("Вы действительно хотите удалить запись?");
    });', View::POS_END);
?>
<div class="attributive-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'title',
            'description',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{create} {view} {update} {delete}',
                'buttons' => [
                    'create' => function ($url) {
                        return Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>', $url);
                    },
                    'delete' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                            'title' => Yii::t('yii', 'Удалить'),
                            'id'=>'delete-button',
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>

</div>
