<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/25
 * Time: 10:09
 */

namespace console\controllers;


use common\models\Source;
use yii\console\Controller;
use yii\db\ActiveQuery;
use yii\helpers\Console;
use yii\helpers\FileHelper;
use yii\web\NotFoundHttpException;

class CollectController extends Controller
{
    public function actionArticle()
    {
        $taskStartTime = microtime(true);
        $sources = Source::find()
            ->joinWith([
                'category' => function(ActiveQuery $query){
                    $query->where(['category_id'=>1]);
                }
            ])
            ->asArray()->all();
        $childs = [];
        foreach ($sources as $source){
            foreach ($source['category'] as $source_category){
                $pid = pcntl_fork();
                if($pid == -1){
                    $this->stdout("Could not fork worker" . $source_category['model'] . "\n");
                    return 1;
                }elseif($pid){
                    $this->stdout("I'm the Parent {$source_category['model']}, PID {$pid}\n");
                    $childs[] = $pid;
                }else{
                    $className = '\console\models\\'.ucfirst(strtolower($source_category['model'])) . 'Spider';
                    if(!class_exists($className)){
                        throw new NotFoundHttpException($className.' Class not found');
                    }
                    \Yii::$app->db->close();
                    $spider = new $className;
                    $spider->process();
                }
            }
        }
        while (count($childs) > 0){
            foreach ($childs as $key => $child){
                $res = pcntl_waitpid($child, $status, WNOHANG);
                if($res == -1 || $res > 0){
                    $this->stdout("$key=>$child\n");
                    unset($childs[$key]);
                }
            }
            sleep(1);
        }
        $lastTime = $this->getElapsedTime($taskStartTime);
        \Yii::info("totalLastTime|" . $lastTime, __METHOD__);
        $this->stdout("success|$lastTime\n");
        $this->stdout("\n");
        return 0;
    }

    public function getElapsedTime($startTime)
    {
        $endTime = microtime(true);
        $elapsedTime = number_format($endTime - $startTime, 4);
        return $elapsedTime;
    }
}