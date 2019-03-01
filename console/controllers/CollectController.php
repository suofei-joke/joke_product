<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/25
 * Time: 10:09
 */

namespace console\controllers;


use console\models\JokejiSpider;
use console\models\JokejiyuanchuangSpider;
use console\models\YiichinaSpider;
use yii\console\Controller;
use yii\base\Exception;
use Resque;
class CollectController extends Controller
{
    public function actionTest()
    {
//        $model = new JokejiyuanchuangSpider();
//        try{
//            $model->addLog('http://www.jokeji.cn/yuanchuangxiaohua/jokehtml/xiaohuayoumo/2019022620394266.htm',
//                '原创笑话', true, 'aaaaaa');
//        }catch (\Exception $e){
//            echo $e->getMessage() . "\n";
//        }
//        die;
        $class = new JokejiyuanchuangSpider();
        $res = $class->getContent('http://www.jokeji.cn/yuanchuangxiaohua/jokehtml/xiaohuayoumo/2019011523510937.htm');
        $res = json_decode($res, true);
        var_dump($res);die;
        if($res){
            $title = $res['title'];
            $content = $res['content'];
            $time = $res['time'];
            $category = $res['category'];
            try{
//                $result = $class->insert($title, $content, $time, $category, $author);
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