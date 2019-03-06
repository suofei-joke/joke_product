<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/5
 * Time: 15:08
 */

namespace console\controllers;


use yii\console\Controller;

class TestController extends Controller
{
    public function actionTest()
    {
        \Yii::$app->aliyunoss->test();
    }
}