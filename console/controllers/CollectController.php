<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/25
 * Time: 10:09
 */

namespace console\controllers;


use console\models\JokejiSpider;
use console\models\YiichinaSpider;
use yii\console\Controller;
use yii\base\Exception;
use Resque;
class CollectController extends Controller
{
    public function actionTest()
    {
//        $className = '\console\models\YiichinaSpider';
//        if(!class_exists($className)){
//            throw new Exception('Yiichina Class does not exist');
//        }
        $class = new JokejiSpider();
        $res = $class->getContent('http://www.jokeji.cn/jokehtml/mj/2019022715390257.htm');
        $res = json_decode($res, true);
        if($res){
            $title = $res['title'];
            $content = $res['content'];
            $time = $res['time'];
            $category = $res['category'];
            try{
                $result = $class->insert($title, $content, $time, $category, $author);
//                $class->addLog($url, $category, $result, $title);
            }catch (\Exception $e){
                echo $e->getMessage().PHP_EOL;
            }
        }

    }
    public function actionIndex()
    {
        Resque::setBackend('localhost:6379');

        $args = [
            'name' => 'Chris'
        ];

        $token = Resque::enqueue('default', '\console\models\MyJob', $args);
        echo $token . "\n";
        $status = new \Resque_Job_Status($token);
        var_dump($status->get());
    }

    public function actionStatus()
    {
        $status = new \Resque_Job_Status('5dee8a204bb8f4db76a34a044ee7985f');
        echo $status->get();
    }
}