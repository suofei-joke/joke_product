<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/25
 * Time: 10:09
 */

namespace console\controllers;


use yii\console\Controller;
use Resque;
class CollectController extends Controller
{
    public function actionIndex()
    {
        Resque::setBackend('localhost:6379');

        $args = [
            'name' => 'Chris'
        ];

        Resque::enqueue('default', '\console\models\MyJob', $args);
    }
}