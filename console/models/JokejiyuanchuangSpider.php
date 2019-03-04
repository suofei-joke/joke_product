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
            $pages = $this->getPages($url);
            if($pages){
                foreach ($pages as $p){
                    $this->urls($p);
                    die;
                }
            }
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
            $this->_url[] = $this->baseUrl . '/' .str_replace($next_page, $i, $next_url);
        }
        return $this->_url;
    }

    /**
     * @desc 获取每页文章列表中文章URL和发布时间
     * @author lijiaxu
     * @date 2019/2/26
     * @param $category
     * @param $url
     */
    private function urls($url)
    {
        $client = new Client();
        $crawler = $client->request('GET', $url);
        $crawler->filter('.ycjoke .txt h2 a')->each(function ($node) use($url){
            if($node){
                try{
                    $a = $node;
                    if($a){
                        $u = $this->baseUrl . '/' . ltrim(trim($a->attr('href')), '/');
                        if(!$this->isGathered($u)){
                            $this->enqueue($u, 'jokejiyuanchuang');
                        }
                    }
                }catch (\Exception $e){
                    $this->addLog($url, 'log', false, $e->getMessage());
                }
            }
        });
    }

    public function getContent($url){
        $client = new Client;
        $crawler = $client->request('GET', $url);
        $node = $crawler->filter('.txt')->eq(0);
        if($node){
            try{
                $category = '原创笑话';

                $title = $node->filter('h1')->text();
                if(mb_strpos($title,'发布于')){
                    $title = explode('发布于', $title)[0];
                }

                $time = $node->filter('span b')->text();
                $timeArr = explode('发布于', $time);
                $name = trim($timeArr[0]);
                $time = strtotime(trim($timeArr[1]));

                $content = $node->filter('ul li')->html();
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