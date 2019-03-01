<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/27
 * Time: 15:42
 */

namespace console\models;


use Goutte\Client;

class JokejiyuanchuangSpider extends ArticleSpider
{
    private $_url;

    public function __construct()
    {
        $this->name = 'jokeji';
        $this->baseUrl = 'http://www.jokeji.cn';
        $this->category = [
            'http://www.jokeji.cn/yuanchuangxiaohua/list/default.htm',
        ];
    }

    public function process()
    {
        foreach ($this->category as $url){
            $this->getPages($url);
        }
    }

    /**
     * @desc 获取当前网站指定分类的分页
     * @author lijiaxu
     * @date 2019/2/26
     * @param $pageUrl
     * @param $category
     * @return array
     */
    private function getPages($pageUrl)
    {
        $client = new Client();
        $crawler = $client->request('GET', $pageUrl);//拿到网页代码
        //拿到分页信息
        $pages = $crawler->filter('.pages a');
        $next_url = ltrim($pages->attr('href'), '/');
        $next_page = $pages->text();
        $max_page = $next_page + 1;
        for($i=$max_page; $i>=1; $i--){
            $this->enqueue($this->baseUrl . '/' .str_replace($next_page, $i, $next_url), 'jokejiyuanchuang');
            die;
//            $this->_url[] = $this->baseUrl . '/' .str_replace($next_page, $i, $next_url);
        }
        return $this->_url;
    }

    public function getContent($url){
        $client = new Client;
        $crawler = $client->request('GET', $url);
        $node = $crawler->filter('.left_up')->eq(0);
        if($node){
            try{
                $category = $node->filter('h1')->eq(0)->filter('a')->eq(1)->text();
                $category = trim($category);

                $title = $node->filter('h1')->eq(0)->text();
                $titleArr = explode('->', $title);
                $title = trim(end($titleArr));

                $time = $node->filter('.pl_ad ul li')->eq(2)->text();
                $time = strtotime(str_replace('发布时间：', '', $time));

                $content = $node->filter('#text110')->html();
                $content = trim($content);

                if($category && $title && $time && $content){
                    return json_encode(['category'=>$category,'title'=>$title,'content'=>$content,'time'=>$time, 'source'=>$this->name, 'author'=>isset($name) ? $name : $this->name]);
                }
            }catch (\Exception $e){
                $this->addLog($url, 'log', false, $e->getMessage());
            }
        }
        return '';
    }
}