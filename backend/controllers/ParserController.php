<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;

class ParserController extends Controller
{
    private $_url;

    private $_href;

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionParsedigbox($url)
    {
        $this->_url = $url;
        Yii::$app->parser->host = 'http://digbox.ru';
        //$href = $this->getHrefProductDigBox();
        $this->_href = array($url);
        $data = $this->getProductDigBox();
        return $this->render('index', [
            'data' => $data,
        ]);
    }

    public function getHrefProductdigbox()
    {
        $data = Yii::$app->parser->loadUsingCurl($this->_url)
            ->createDomDocument()
            ->createDomXpath()
            ->parseHref()
            ->saveToFile()
            ->endParse();
        return $data->href;
    }

    public function getProductdigbox()
    {
        foreach ($this->_href as $value) {
            $data = Yii::$app->parser->loadUsingCurl($value)
                ->createDomDocument()
                ->createDomXpath()
                ->parseName()
                ->parseModel()
                ->parseArticle()
                ->parseOverview()
                ->parseAttribute()
                //->parseImgRemoteHref()
                //->saveImage()
                ->endParse();
            return $data;
        }
    }
}
