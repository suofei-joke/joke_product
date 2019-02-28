<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/26
 * Time: 11:21
 */

namespace console\models;


use Goutte\Client;

class YiichinaSpider extends ArticleSpider
{
    private $_url;

    public function __construct()
    {
        $this->name = 'Yiichina';
        $this->baseUrl = 'http://yiichina.com';
        $this->category = [
            '教程'=>'http://www.yiichina.com/tutorial',
//            '扩展'=>'http://www.yiichina.com/extension',
//            '源码'=>'http://www.yiichina.com/code',
        ];
    }

    public function process()
    {
        foreach ($this->category as $category=>$url){
            $pages = $this->getPages($url, $category);
            if($pages){
                foreach ($pages as $p){
                    $this->urls($category, $p);
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
    private function getPages($pageUrl, $category)
    {
        $client = new Client();
        $crawler = $client->request('GET', $pageUrl);
        //获取分页
        $crawler->filter('.media-list .pagination li a')->each(function ($node) use($pageUrl, $category){
            if($node){
                try{
                    $this->_url[] = $this->baseUrl . '/' .ltrim(trim($node->attr('href')), '/');
                }catch (\Exception $e){
                    $this->addLog($pageUrl, $category, false, $e->getMessage());
                }
            }
        });
        return array_unique($this->_url);
    }

    /**
     * @desc 获取每页文章列表中文章URL和发布时间
     * @author lijiaxu
     * @date 2019/2/26
     * @param $category
     * @param $url
     */
    private function urls($category, $url)
    {
        $client = new Client();
        $crawler = $client->request('GET', $url);
        $crawler->filter('.media-list .media')->each(function ($node) use($category, $url){
            if($node){
                try{
                    $a = $node->filter('.media-body h2 a');
                    if($a){
                        $u = $this->baseUrl.trim($a->attr('href'));
                        if(!$this->isGathered($u)){
                            $this->enqueue($category, $u, 'yiichina');
                        }
                    }
                }catch (\Exception $e){
                    $this->addLog($url, $category, false, $e->getMessage());
                }
            }
        });
    }

    public function getContent($url, $category){
        $client = new Client;
        $crawler = $client->request('GET', $url);
        $node = $crawler->filter('.col-lg-9')->eq(0);
        if($node){
            try{
                $title = $node->filter('.page-header h1');
                $time = $node->filter('.action span')->eq(1);
                $content = $node->filter('.markdown');
                if($title && $time){
                    $title = trim($title->text());
                    $time = trim($time->text());
                    $content = trim($content->html());
                    return json_encode(['title'=>$title,'content'=>$content,'time'=>$time]);
                }
            }catch (\Exception $e){
                $this->addLog($url, $category, false, $e->getMessage());
            }
        }
        return '';
    }
}