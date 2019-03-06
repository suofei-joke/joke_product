<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/25
 * Time: 13:49
 */

namespace console\models;


use common\models\Article;
use yii\base\Exception;

class ImageJob
{
    public function perform()
    {
        $args = $this->args;
        file_put_contents('/tmp/ljx.log', json_encode($args). "\n", FILE_APPEND);
        $category = $args['category'];
        $url = trim($args['url']);
        $baseClassName = $args['className'];
        $publishTime = $args['publishTime'];

        if(Article::find()->where(['url'=>$url])->exists()){
            return 0;
        }
        $className = '\console\models\\'.ucfirst(strtolower($baseClassName)).'Spider';
        if(!class_exists($className)){
            throw new Exception($baseClassName.' Class does not exist');
        }
        $class = new $className;
        $res = $class->getContent($url);
        $res = json_decode($res, true);
        if($res){
            if(count($res) == count($res, 1)){
                $title = $res['title'];
                $content = $res['content'];
                $time = $res['time'];
                $time = $publishTime ?: $time;
                $category = $category ?: $res['category'];
                $author = isset($res['author']) ? $res['author'] : '';
                try{
                    $result = $class->insert($title, $content, $url, $time, $category, $author);
                }catch (\Exception $e){
                    echo $e->getMessage().PHP_EOL;
                }
            }else{
                foreach ($res as $value){
                    $title = $value['title'];
                    $content = $value['content'];
                    $time = $value['time'];
                    $time = $publishTime ?: $time;
                    $category = $category ?: $value['category'];
                    $author = isset($value['author']) ? $value['author'] : '';
                    try{
                        $result = $class->insert($title, $content, $url, $time, $category, $author);
                    }catch (\Exception $e){
                        echo $e->getMessage().PHP_EOL;
                    }
                }
            }
        }
    }
}