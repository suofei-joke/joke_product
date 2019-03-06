<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/4
 * Time: 15:07
 */

namespace console\models;


use Goutte\Client;

class Jokeji_gaoxiaoSpider extends ImageSpider
{
    private $_url;

    public function __construct()
    {
        $this->name = 'jokeji';
        $this->baseUrl = 'http://gaoxiao.jokeji.cn';
        $this->category = 'http://gaoxiao.jokeji.cn/list/list_2.htm';
    }

    public function process()
    {
        $pages = $this->getPages($this->category);
        if ($pages) {
            foreach ($pages as $p) {
                $this->urls($p);
                die;
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
        $pages = $crawler->filter('.tags_page a');
        //获取最大页数
        $max_key = count($pages) - 1;
        $max_url = ltrim($pages->eq($max_key)->attr('href'), '/');//最大链接后缀
        $max_page = str_replace(['list/list_', '.htm'], '', $max_url);
        for($i=1; $i<=$max_page; $i++){
            $this->_url[] = $this->baseUrl . '/' .str_replace($max_page, $i, $max_url);
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
        $crawler->filter('.list_list ul li span')->each(function ($node) use($url){
            if($node){
                try{
                    $a = $node->filter('a')->eq(0);
                    if($a){
                        $u = $this->baseUrl . '/' . ltrim(trim($a->attr('href')), '/');
                        echo $u . "\n";
//                        if(!$this->isGathered($u)){
//                            $this->enqueue($u, 'jokeji');
//                        }
                    }
                }catch (\Exception $e){
                    $this->addLog($url, 'log', false, $e->getMessage());
                }
            }
        });
    }
}