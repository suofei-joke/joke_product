<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/4
 * Time: 15:07
 */

namespace console\models;


class Jokeji_gaoxiaoSpider extends ArticleSpider
{
    private $_url;

    public function __construct()
    {
        $this->name = 'jokeji';
        $this->baseUrl = 'http://www.jokeji.cn';
        $this->category = 'http://www.jokeji.cn/Keyword.htm';
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
}