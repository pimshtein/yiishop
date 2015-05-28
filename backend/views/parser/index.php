<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel common\models\BrandSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Parser';
$this->params['breadcrumbs'][] = $this->title;
$this->params['url'] = Yii::$app->request->getQueryParam('url');
?>
<div class="row">
    <div class="col-sm-12">

        <h1><?= Html::encode($this->title) ?></h1>

        <?= Html::beginForm(['parser/parsedigbox'], 'get') ?>

        <div class="form-group">
            <?= Html::textInput('url', $this->params['url'],
                ['class' => 'form-control', 'placeholder' => 'Input url']); ?>
        </div>

        <?= Html::submitButton('Распарсить', ['class' => 'btn btn-primary']) ?>

        <?= Html::endForm() ?>

        <?php if (isset($href)) { ?>

        <h3>Вывод:</h3>

        <p class="thumbnail">

            <?php foreach ($data->href as $value) {
                echo 'Ссылка: ' . $value . '<br />';
            }

            }; ?>

        </p>

        <?php if (isset($data->name)) { ?>

        <h3>Товар:</h3>

        <p class="thumbnail">

            <?php echo 'Ссылка: ' . $this->params['url'] . '<br />' .
                'Название: ' . $data->name . '<br />' .
                'Артикул: ' . $data->article . '<br />' .
                'Модель: ' . $data->model . '<br />';
            }; ?>

        </p>

        <?php if (isset($data->overview)) { ?>

        <h3>Описание:</h3>

        <p class="thumbnail">

            <?= $data->overview;
            } ?>

        </p>

        <?php if (isset($data->attribute)) { ?>

        <h3>Атрибуты:</h3>

        <p class="thumbnail">

            <?php foreach ($data->attribute as $key => $value) {
                echo $key . ': ' . $value . '<br />';
            }
            } ?>

        </p>

        <?php if (isset($data->imgRemoteHref)) { ?>

        <h3>Ссылки на изображения товара:</h3>

        <div class="btn-group-vertical" role="group" aria-label="...">

            <?php foreach ($data->imgRemoteHref as $value) {
                echo Html::a($value, $value, ['class' => 'btn btn-default btn-sm', 'target' => '_blank']);
            }

            } ?>

        </div>

    </div>
</div>