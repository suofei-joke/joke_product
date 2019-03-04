<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/25
 * Time: 13:57
 */

namespace console\models;


use common\models\Article;
use common\models\ArticleTag;
use common\models\Gather;
use common\models\Tag;

class ArticleSpider
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
        \Resque::enqueue('article_spider', 'console\models\ArticleJob', ['category'=>$category, 'url'=>$url, 'className'=>$className, 'publishTime'=>$publishTime]);
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
    public static function insert($title, $content, $url, $published_at, $tag='未知', $author='')
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try{
            $tagModel = Tag::find()->where(['name'=>$tag])->one();
            if(!$tagModel){
                $tagModel = new Tag();
                $tagModel->name = $tag;
                $tagModel->article_count = 1;
                $tagModel->save(false);
            }else{
                Tag::updateAllCounters(['article_count'=>1], ['name'=>$tag]);
            }
            $article = new Article();
            $article->title = $title;
            $article->content = $content;
            $article->url = $url;
            $article->author = $author;
            $article->tag_id = $tagModel->id;
            $article->status = Article::STATUS_GATHER;
            $article->published_at = $published_at;
            $res = $article->save(false);

            $articleTag = new ArticleTag();
            $articleTag->article_id = $article->id;
            $articleTag->tag_id = $tagModel->id;
            $articleTag->save(false);
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