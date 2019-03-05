<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/27
 * Time: 15:42
 */

namespace console\models;


use Goutte\Client;

class JokejiSpider extends ArticleSpider
{
    private $_url;
    private static $page_param = 'me_page';

    public function __construct()
    {
        $this->name = 'jokeji';
        $this->baseUrl = 'http://www.jokeji.cn';
        $this->category = [
            'http://www.jokeji.cn/Keyword.htm',
        ];
    }

    public function process()
    {
        file_put_contents('/tmp/ljx.log', 'joke'."\n", FILE_APPEND);
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
        $pages = $crawler->filter('.main_title table tr td a');
        //获取最大页数
        $max_key = count($pages) - 1;
        $max_url = ltrim($pages->eq($max_key)->attr('href'), '/');//最大链接后缀
        $total_page = parse_url($max_url)['query'];//分解链接拿到参数信息
        parse_str($total_page, $me_page);
        $max_page = $me_page[self::$page_param];
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
        $crawler->filter('.main_14')->each(function ($node) use($url){
            if($node){
                try{
                    $a = $node;
                    if($a){
                        $u = $this->baseUrl . '/' . ltrim(trim($a->attr('href')), '/');
                        if(!$this->isGathered($u)){
                            $this->enqueue($u, 'jokeji');
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