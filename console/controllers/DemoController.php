<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/25
 * Time: 10:02
 */
namespace console\controllers;

use yii\console\Controller;

class DemoController extends Controller
{
    public function actionDemo()
    {
        echo "demo\n";
        return 0;
    }
}