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
    public static function insert($title, $content, $url, $published_at, $tag='未知', $author='')
    {
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