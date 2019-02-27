<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/25
 * Time: 13:33
 */

namespace console\controllers;


use yii\console\Controller;

class QueueController extends Controller
{
    public function actionRun()
    {
        $QUEUE = getenv('QUEUE');
        if(empty($QUEUE)){
            die("Set QUEUE env var containing the list of queues to work.\n");
        }

        $REDIS_BACKEND = getenv('REDIS_BACKEND');
        if(!empty($REDIS_BACKEND)){
            \Resque::setBackend($REDIS_BACKEND);
        }

        $logLevel = 0;
        $LOGGING = getenv('LOGGING');
        $VERBOSE = getenv('VERBOSE');
        $VVERBOSE = getenv('VVERBOSE');
        if(!empty($LOGGING) || !empty($VERBOSE)){
            $logLevel = \Resque_Worker::LOG_NORMAL;
        }elseif (!empty($VVERBOSE)){
            $logLevel = \Resque_Worker::LOG_VERBOSE;
        }

        $APP_INCLUDE = getenv('APP_INCLUDE');
        if($APP_INCLUDE){
            if(!file_exists($APP_INCLUDE)){
                die('APP_INCLUDE ('.$APP_INCLUDE.") does not exist.\n");
            }
            require_once $APP_INCLUDE;
        }

        $interval = 5;
        $INTERVAL = getenv('INTERVAL');
        if(!empty($INTERVAL)){
            $interval = $INTERVAL;
        }

        $count = 1;
        $COUNT = getenv('COUNT');
        if(!empty($COUNT) && $COUNT > 1){
            $count = $COUNT;
        }

        if($count > 1){
            for ($i = 0; $i < $count; ++$i){
                $pid = pcntl_fork();
                if($pid == -1){
                    die("Could not fork worker ".$i."\n");
                }elseif (!$pid){
                    $queues = explode(',', $QUEUE);
                    $worker = new \Resque_Worker($queues);
                    $worker->logLevel = $logLevel;
                    fwrite(STDOUT, '*** Starting worker '.$worker."\n");
                    $worker->work($interval);
                    break;
                }
            }
        }else{
            $queues = explode(',', $QUEUE);
            $worker = new \Resque_Worker($queues);
            $worker->logLevel = $logLevel;

            $PIDFILE = getenv('PIDFILE');
            if($PIDFILE){
                file_put_contents($PIDFILE, getmypid()) or die('Could not write PID information to ' . $PIDFILE);
            }
file_put_contents('/tmp/ljx.log', '['.date('Y-m-d H:i:s').'] queue start'."\n", FILE_APPEND);
            fwrite(STDOUT, '*** Starting worker '.$worker."\n");
            $worker->work($interval);
        }
    }
}