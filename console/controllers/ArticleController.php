<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/25
 * Time: 13:45
 */

namespace console\controllers;


use yii\console\Controller;
use yii\web\NotFoundHttpException;

class ArticleController extends Controller
{
    public function actionRun($name)
    {
        $className = '\console\models\\'.ucfirst(strtolower($name)) . 'Spider';
        if(!class_exists($className)){
            throw new NotFoundHttpException($className.' Class not found');
        }

        $spider = new $className;
        $spider->process();
    }
}