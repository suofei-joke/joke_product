<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/5
 * Time: 15:08
 */

namespace console\controllers;


use console\models\Jokeji_imgSpider;
use yii\console\Controller;
use yii\helpers\FileHelper;

class TestController extends Controller
{
    public function actionTest()
    {
//        $mime = FileHelper::getMimeType('d:/test.jpg');
//        var_dump(FileHelper::getMimeTypeByExtension('d:/test.jpg'));
//
//        var_dump(FileHelper::getExtensionsByMimeType($mime));die;
        $model = new Jokeji_imgSpider();
        $model->getContent('http://gaoxiao.jokeji.cn/GrapHtml/quweigaoxiao/20181220222051.htm');
    }
}