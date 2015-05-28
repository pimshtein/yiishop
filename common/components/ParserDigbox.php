<?php
namespace common\components;

use Yii;
use yii\base\Object;

/**
 * @property string $pathToCookieFile
 * @property string $loginUrl
 * @property string $title
 * @property int $timestamp
 * @property string $content
 *
 */
class ParserDigBox extends Object
{
    /**
     * Uri to authorize the resource to parse the closed hide theme
     */
    const REQUEST_URI_LOGIN = 'login/login';
    /**
     * The file name to save the file cookies
     */
    const COOKIES_FILE_NAME = 'cookies.txt';
    /**
     * @var string data parse
     */
    private $_data;
    /**
     * @var string information about the host server from which to parse the data
     */
    public $host;
    /**
     * @var boolean information about the need to login
     */
    public $needLogin;
    /**
     * @var boolean information about the need to Post request
     */
    public $needPost;
    /**
     * @var string username for the authorization in the resource
     */
    public $username;
    /**
     * @var string password for the authorization in the resource
     */
    public $password;
    /**
     * @var array configuration cURL
     */
    public $curlOpt;
    /**
     * @var string title
     */
    private $_title;
    /**
     * @var array href
     */
    private $_href;
    /**
     * @var string href
     */
    private $_name;
    /**
     * @var string article
     */
    private $_article;
    /**
     * @var string model
     */
    private $_model;
    /**
     * @var string content
     */
    private $_content;
    /**
     * @var string overview
     */
    private $_overview;
    /**
     * @var string attributes
     */
    private $_attribute;
    /**
     * @var array remote href for images
     */
    private $_imgRemoteHref;
    /**
     * @var int timestamp
     */
    private $_timestamp;
    /**
     * @var \DOMXPath xpath
     */
    private $_xpath;
    /**
     * @var \DOMXPath dom
     */
    private $_dom;

    /**
     * Retrieving configuration cURL
     * @param $nameOpt string name option for cURL
     * Maintaining the value of: userAgent and header
     * @return bool|string|array opt curl
     */
    protected function getCurlOpt($nameOpt)
    {
        if ($nameOpt !== 'userAgent' && $nameOpt !== 'header') {
            return false;
        }
        return $this->curlOpt[$nameOpt];
    }

    /**
     * Getting the full url handler request authorization
     * View as host + url login
     * @return string url authorization
     */
    protected function getLoginUrl()
    {
        return $this->host . self::REQUEST_URI_LOGIN;
    }

    /**
     * Creating a post request string for authentication using cURL
     * @return string post request
     */
    protected function createPostRequestForCurl()
    {
        return 'login=' . $this->username . '&password=' . $this->password . '&remember=1';
    }

    /**
     * Getting the path to the file cookies.
     * The file is stored application.runtime + cookie file name
     * @param string $cookieFileName string cookie file name. Defaults is self::COOKIES_FILE_NAME
     * @return string
     */
    protected function getPathToCookieFile($cookieFileName = self::COOKIES_FILE_NAME)
    {
        return Yii::getAlias('@app/runtime') . DIRECTORY_SEPARATOR . $cookieFileName;
    }

    /**
     * Loading data using cURL, transferred on url page for further processing.
     * @param string $url string url from which the data will be taken
     * @return \common\components\ParserDigBox
     * @throws \Exception
     */
    public function loadUsingCurl($url)
    {
        $ch = curl_init();
        if ($this->needLogin) {
            curl_setopt($ch, CURLOPT_URL, $this->host);
            curl_setopt($ch, CURLOPT_REFERER, $url);
        } else {
            curl_setopt($ch, CURLOPT_URL, $url);
        }
        // Set this option to a non-zero value if you want PHP to
        // fail silently if the HTTP code returned is greater than 300.
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        // Set this option to a non-zero value to follow any
        // "Location:" header, which the server sends as part of the HTTP header
        // (Note that this recursive, PHP will follow all the "Location:" -header that are available)        
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        // 1 to return the transfer as a string of
        // the return value of curl_exec() instead of outputting it out directly.
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getCurlOpt('header'));
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->pathToCookieFile);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->pathToCookieFile);
        // 1 to force the use of a new connection instead of a cached one.
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->getCurlOpt('userAgent'));
        if ($this->needPost) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->createPostRequestForCurl());
        }
        $this->_data = curl_exec($ch);
        if ($this->_data === false) {
            throw new \Exception(curl_errno($ch) . ': ' . curl_error($ch));
        }
        curl_close($ch);
        Yii::info(Yii::t('apiResponse', 'Loaded data'), 'apiResponse');
        return $this;
    }

    public function getNameImageFromUrl($url)
    {
        return substr($url, strripos($url, '/') + 1, strlen($url) - strripos($url, '/'));
    }

    /**
     * Saving images using cURL
     * @param string $url string url from which the data will be taken
     * @param string $dirName string path to store images
     * @return \common\components\ParserDigBox
     * @throws \Exception
     */
    public function grabImageUsingCurl($url, $dirName)
    {
        sleep(5);
        $ch = curl_init();
        if ($this->needLogin) {
            curl_setopt($ch, CURLOPT_URL, $this->host);
            curl_setopt($ch, CURLOPT_REFERER, $url);
        } else {
            curl_setopt($ch, CURLOPT_URL, $url);
        }
        // Set this option to a non-zero value if you want PHP to
        // fail silently if the HTTP code returned is greater than 300.
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        // Set this option to a non-zero value to follow any
        // "Location:" header, which the server sends as part of the HTTP header
        // (Note that this recursive, PHP will follow all the "Location:" -header that are available)        
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        // 1 to return the transfer as a string of
        // the return value of curl_exec() instead of outputting it out directly.
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getCurlOpt('header'));
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->pathToCookieFile);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->pathToCookieFile);
        // 1 to force the use of a new connection instead of a cached one.
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->getCurlOpt('userAgent'));
        if ($this->needPost) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->createPostRequestForCurl());
        }
        $this->_data = curl_exec($ch);
        if ($this->_data === false) {
            throw new \Exception(curl_errno($ch) . ': ' . curl_error($ch));
        }
        curl_close($ch);
        if (!is_dir($dirName))
        {
            if (mkdir($dirName, 0755, true) === false) {
                throw new \Exception('Could not create directory.');
            }
        }
        $fileName = $dirName . '/' . $this->getNameImageFromUrl($url);
        if (file_exists($fileName)) {
            unlink($fileName);
        }
        $fp = fopen($fileName, 'x');
        if ($fp === false) {
            throw new \Exception('Could not open file to write.');
        }
        if (fwrite($fp, $this->_data) === false) {
            throw new \Exception('Could not save file.');
        }
        fclose($fp);
        Yii::info(Yii::t('apiResponse', 'Image ' . $url . ' was saved'), 'apiResponse');
        return $this;
    }

    /**
     * @return \common\components\ParserDigBox
     */
    public function saveImage()
    {
        if ($this->_imgRemoteHref == '') {
            Yii::info(Yii::t('apiResponse', 'Error array of product images is empty'), 'apiResponse');
            return $this;
        }
        foreach ($this->_imgRemoteHref as $value) {
            $filePath = Yii::getAlias('@backend/web/images/' . $this->_article . '/');
            $this->grabImageUsingCurl($value, $filePath);
        }
        return $this;
    }

    /**
     * @return \common\components\ParserDigBox
     */
    public function createDomDocument()
    {
        $this->_dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        if ($this->_dom->loadHTML($this->_data)) {
            Yii::info(Yii::t('apiResponse', 'Create DomDocument'), 'apiResponse');
        } else {
            Yii::info(Yii::t('apiResponse', 'An error occurred when creating an object of class DOMDocument'),
                'apiResponse');
        }
        libxml_use_internal_errors(false);
        return $this;
    }

    /**
     * @return \common\components\ParserDigBox
     */
    public function createDomXpath()
    {
        $this->_xpath = new \DOMXPath($this->_dom);
        Yii::info(Yii::t('apiResponse', 'Create DomXpath'), 'apiResponse');
        return $this;
    }

    /**
     * @return \common\components\ParserDigBox
     */
    public function parseTitle()
    {
        $xpathQuery = '*//h1';
        $nodes = $this->_xpath->query($xpathQuery, $this->_dom);
        if ($nodes->length === 0) {
            Yii::info(Yii::t('apiResponse', 'Error parse title'), 'apiResponse');
            return $this;
        }
        $this->_title = $nodes->item(0)->nodeValue;
        Yii::info(Yii::t('apiResponse', 'Parse title'), 'apiResponse');
        return $this;
    }

    /**
     * @return \common\components\ParserDigBox
     */
    public function parseHref()
    {
        $xpathQuery = "*//div[@class='item']/a/@href";
        $nodes = $this->_xpath->query($xpathQuery, $this->_dom);
        if ($nodes->length === 0) {
            Yii::info(Yii::t('apiResponse', 'Error parse href'), 'apiResponse');
            return $this;
        }

        for ($i = 0; $i < $nodes->length; $i++) {
            $this->_href[] = $nodes->item($i)->nodeValue;
        }
        Yii::info(Yii::t('apiResponse', 'Parse href'), 'apiResponse');
        return $this;
    }

    /**
     * @return \common\components\ParserDigBox
     */
    public function parseName()
    {
        $xpathQuery = "*//h1[@class='slide-title']";
        $nodes = $this->_xpath->query($xpathQuery, $this->_dom);
        if ($nodes->length === 0) {
            Yii::info(Yii::t('apiResponse', 'Error parse name'), 'apiResponse');
            return $this;
        }
        $this->_name = $nodes->item(0)->nodeValue;
        Yii::info(Yii::t('apiResponse', 'Parse href'), 'apiResponse');
        return $this;
    }

    /**
     * @return \common\components\ParserDigBox
     */
    public function parseModel()
    {
        $this->_model = $this->_name;
        if ($this->_model == '') {
            Yii::info(Yii::t('apiResponse', 'Error parse model'), 'apiResponse');
            return $this;
        }
        Yii::info(Yii::t('apiResponse', 'Parse href'), 'apiResponse');
        return $this;
    }

    /**
     * @return \common\components\ParserDigBox
     */
    public function parseArticle()
    {
        $xpathQuery = "*//span[@class='item-id pull-l']/strong";
        $nodes = $this->_xpath->query($xpathQuery, $this->_dom);
        if ($nodes->length === 0) {
            Yii::info(Yii::t('apiResponse', 'Error parse href'), 'apiResponse');
            return $this;
        }
        $this->_article = $nodes->item(0)->nodeValue;
        Yii::info(Yii::t('apiResponse', 'Parse href'), 'apiResponse');
        return $this;
    }

    /**
     * @param $saveInnerHtml boolean if true return inner html with text
     * if false - return text without tags
     * @return \common\components\ParserDigBox
     */
    public function parseOverview($saveInnerHtml = false)
    {
        $xpathQuery = "*//section[@id='promo']/div";
        $nodes = $this->_xpath->query($xpathQuery, $this->_dom);
        if ($nodes->length === 0) {
            Yii::info(Yii::t('apiResponse', 'Error parse overview'), 'apiResponse');
            return $this;
        }
        if ($saveInnerHtml) {
            $this->_overview = $this->getInnerHTML($nodes->item(0));
        } else {
            $this->_overview = $nodes->item(0)->nodeValue;
        }
        Yii::info(Yii::t('apiResponse', 'Parse href'), 'apiResponse');
        return $this;
    }

    /**
     * @return \common\components\ParserDigBox
     */
    public function parseImgRemoteHref()
    {
        $xpathQuery = "*//li/a[@class='fancybox-thumbs']/@href";
        $nodes = $this->_xpath->query($xpathQuery, $this->_dom);
        if ($nodes->length === 0) {
            Yii::info(Yii::t('apiResponse', 'Error parse overview'), 'apiResponse');
            return $this;
        }
        for ($i = 0; $i < $nodes->length; $i++) {
            $this->_imgRemoteHref[] = $this->host . $nodes->item($i)->nodeValue;
        }
        Yii::info(Yii::t('apiResponse', 'Parse href'), 'apiResponse');
        return $this;
    }

    /**
     * @return \common\components\ParserDigBox
     */
    public function parseAttribute()
    {
        $xpathQueryKey = "*//section[@id='features']/table//tr/td[1]";
        $xpathQueryValue = "*//section[@id='features']/table//tr/td[2]";
        $nodesKey = $this->_xpath->query($xpathQueryKey, $this->_dom);
        $nodesValue = $this->_xpath->query($xpathQueryValue, $this->_dom);
        if ($nodesKey->length === 0) {
            Yii::info(Yii::t('apiResponse',
                'Error parse attribute key'),
                'apiResponse');
            return $this;
        }
        if ($nodesValue->length === 0) {
            Yii::info(Yii::t('apiResponse',
                'Error parse attribute value'),
                'apiResponse');
            return $this;
        }
        if ($nodesKey->length !== $nodesValue->length) {
            Yii::info(Yii::t('apiResponse',
                'Error length of array key in attribute does not equal length of array value in attributes'),
                'apiResponse');
            return $this;
        }
        for ($i = 0; $i < $nodesKey->length; $i++) {
            // Delete control symbols from keys and values.
            // p{C} - invisible control symbols. /u - we are work with unicode.
            $this->_attribute[preg_replace('/\p{C}+/u', '', $nodesKey->item($i)->nodeValue)] =
                preg_replace('/\p{C}+/u', '', $nodesValue->item($i)->nodeValue);
        }
        Yii::info(Yii::t('apiResponse', 'Parse href'), 'apiResponse');
        return $this;
    }

    /**
     * @param $node \DOMNode node from xpath query
     * @return string innerHtml inner html code
     */
    public function getInnerHTML($node)
    {
        $innerHTML = '';
        $children = $node->childNodes;
        foreach ($children as $child) {
            $tmp_doc = new \DOMDocument();
            $tmp_doc->appendChild($tmp_doc->importNode($child, true));
            $innerHTML .= $tmp_doc->saveHTML();
        }
        return $innerHTML;
    }

    /**
     * @return \common\components\ParserDigBox
     */
    public function parseTimestamp()
    {
        $xpathQuery = '*//p[@id="pageDescription"]/a/abbr';
        $nodes = $this->_xpath->query($xpathQuery, $this->_dom);
        if ($nodes->length === 0) {
            Yii::info(Yii::t('apiResponse', 'Error parse timestamp'), 'apiResponse');
            return $this;
        }
        $this->_timestamp = $nodes->item(0)->getAttribute('data-time');
        Yii::info(Yii::t('apiResponse', 'Parse timestamp'), 'apiResponse');
        return $this;
    }

    /**
     * @return \common\components\ParserDigBox
     */
    public function parseContent()
    {
        $xpathQuery = '*//blockquote[@class="messageText ugc baseHtml"]';
        $nodes = $this->_xpath->query($xpathQuery, $this->_dom);
        if ($nodes->length === 0) {
            Yii::info(Yii::t('apiResponse', 'Error parse content'), 'apiResponse');
            return $this;
        }
        $this->_content = $nodes->item(0)->nodeValue;
        Yii::info(Yii::t('apiResponse', 'Parse content'), 'apiResponse');
        return $this;
    }

    /**
     * @return \common\components\ParserDigBox
     */
    public function endParse()
    {
        if (isset($this->_data)) {
            Yii::info(Yii::t('apiResponse', 'End parse'), 'apiResponse');
        } else {
            Yii::info(Yii::t('apiResponse', 'Some data were not received'), 'apiResponse');
        }
        return $this;
    }

    /**
     * @return \common\components\ParserDigBox
     */
    public function saveToFile()
    {
        $fileName = Yii::getAlias('@backend/runtime/pageTest.html');
        if (file_put_contents($fileName, $this->_data)) {
            Yii::info(Yii::t('apiResponse', 'End parse'), 'apiResponse');
        } else {
            Yii::info(Yii::t('apiResponse', 'Could not save file'), 'apiResponse');
        }
        return $this;
    }

    /**
     * @return string title
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * @return string href
     */
    public function getHref()
    {
        return $this->_href;
    }

    /**
     * @return string data
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * @return int timestamp
     */
    public function getTimestamp()
    {
        return $this->_timestamp;
    }

    /**
     * @return string content
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
     * @return string name of product
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @return string model of product
     */
    public function getModel()
    {
        return $this->_model;
    }

    /**
     * @return string article of product
     */
    public function getArticle()
    {
        return $this->_article;
    }

    /**
     * @return string overview of product
     */
    public function getOverview()
    {
        return $this->_overview;
    }

    /**
     * @return string attributes of product
     */
    public function getAttribute()
    {
        return $this->_attribute;
    }

    /**
     * @return string attributes of product
     */
    public function getImgRemoteHref()
    {
        return $this->_imgRemoteHref;
    }
}
