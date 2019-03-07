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

class TestController extends Controller
{
    public function actionTest()
    {
        $model = new Jokeji_imgSpider();
        $model->getContent('http://gaoxiao.jokeji.cn/GrapHtml/quweigaoxiao/20181220222051.htm');
    }
}