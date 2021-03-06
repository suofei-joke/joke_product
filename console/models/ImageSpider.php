<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/25
 * Time: 13:57
 */

namespace console\models;


use common\models\Article;
use common\models\ArticleEntity;
use common\models\ArticleTag;
use common\models\Gather;

class ImageSpider
{
    protected $category = [];//网站文章分类
    protected $baseUrl = '';//网站域名
    protected $name = '';//网站名称

    /**
     * 判断文章是否采集
     * @param $url
     * @return bool
     */
    protected function isGathered($url)
    {
        $gather = Article::find()->where(['url'=>trim($url)])->one();
        return $gather ? true : false;
    }

    /**
     * @desc 插入URL队列
     * @author lijiaxu
     * @date 2019/2/26
     * @param $category
     * @param $url
     * @param $className
     * @param string $publishTime
     */
    public function enqueue($url, $className, $category = '', $publishTime = '')
    {
        \Resque::enqueue('img_spider', 'console\models\ImageJob', ['category'=>$category, 'url'=>$url, 'className'=>$className, 'publishTime'=>$publishTime]);
    }

    /**
     * @desc 将文章插入数据库
     * @author lijiaxu
     * @date 2019/2/26
     * @param $title
     * @param $content
     * @param $published_at
     * @param string $tag
     * @return bool
     */
    public static function insert($title, $content, $oss, $url, $published_at, $tag='未知', $author='')
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try{
            $tagModel = ArticleTag::find()->where(['name'=>$tag])->one();
            if(!$tagModel){
                $tagModel = new ArticleTag();
                $tagModel->name = $tag;
                $tagModel->article_count = 1;
                $tagModel->save(false);
            }else{
                ArticleTag::updateAllCounters(['article_count'=>1], ['name'=>$tag]);
            }
            foreach ($oss as $ossVal){
                if(!ArticleEntity::find()->where(['md5'=>$ossVal['md5']])->exists()){
                    $entityModel = new ArticleEntity();
                    $entityModel->md5 = $ossVal['md5'];
                    $entityModel->mime = $ossVal['mime'];
                    $entityModel->entity = $ossVal['entity'];
                    $entityModel->save(false);
                }
            }
            $article = new Article();
            $article->title = $title;
            $article->content = $content;
            $article->url = $url;
            $article->author = $author;
            $article->tag_id = $tagModel->id;
            $article->type = Article::$ARTICLE_TYPE['image'];
            $article->status = Article::STATUS_GATHER;
            $article->published_at = $published_at;
            $res = $article->save(false);
            $transaction->commit();
        }catch (\Exception $e){
            $res = false;
            $transaction->rollBack();
            file_put_contents('/tmp/ljx.log', $e->getMessage() . "\n", FILE_APPEND);
        }
        return $res ? true : false;
    }

    public function addLog($url, $category, $res, $result)
    {
        $gather = new Gather();
        $gather->name = $this->name;
        $gather->category = $category;
        $gather->url = md5($url);
        $gather->url_org = $url;
        $gather->res = $res;
        $gather->result = $result;
        $gather->save();
    }
}