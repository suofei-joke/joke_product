<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/25
 * Time: 10:09
 */

namespace console\controllers;


use common\models\Source;
use common\models\SourceCategory;
use yii\console\Controller;
use yii\db\ActiveQuery;
use yii\web\NotFoundHttpException;

class CollectController extends Controller
{
    public function actionArticle()
    {
        $sources = Source::find()
            ->joinWith([
                'category' => function(ActiveQuery $query){
                    $query->where(['category_id'=>1]);
                }
            ])
            ->asArray()->all();
        foreach ($sources as $source){
            foreach ($source['category'] as $source_category){
                $pid = pcntl_fork();
                if($pid == -1){
                    die("Could not fork worker" . $source_category['model'] . "\n");
                }elseif(!$pid){
                    $className = '\console\models\\'.ucfirst(strtolower($source_category['model'])) . 'Spider';
                    if(!class_exists($className)){
                        throw new NotFoundHttpException($className.' Class not found');
                    }

                    $spider = new $className;
                    $spider->process();
                }
            }
        }
    }
}