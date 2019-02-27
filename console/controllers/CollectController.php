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

        $token = Resque::enqueue('default', '\console\models\MyJob', $args);
        echo $token . "\n";
    }

    public function actionStatus()
    {
        $status = new \Resque_Job_Status('5dee8a204bb8f4db76a34a044ee7985f');
        echo $status->get();
    }
}