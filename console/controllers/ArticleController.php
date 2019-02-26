<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/25
 * Time: 13:45
 */

namespace console\controllers;


use common\models\Gather;
use yii\console\Controller;
use yii\web\NotFoundHttpException;

class ArticleController extends Controller
{
    public function actionTest()
    {
        $gather = new Gather();
        $gather->name = 'a';
        $gather->category = 'b';
        $gather->url = 'c';
        $gather->url_org = 'd';
        $gather->res = 0;
        $gather->result = 'e';
        $gather->save();
    }
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