<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/4
 * Time: 15:07
 */

namespace console\models;


use common\components\Aliyunoss;
use common\models\ArticleEntity;
use Goutte\Client;
use yii\helpers\FileHelper;

class Jokeji_imgSpider extends ImageSpider
{
    private $_url;
    private $content;
    private $ossInfo = [];
    private $ossDir = 'joke/gaoxiao/';

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
                        if(!$this->isGathered($u)){
                            $this->enqueue($u, 'jokeji_img');
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
        $node = $crawler->filter('.pic_pview')->eq(0);
        if($node){
            try{
                $category = $node->filter('h2 a')->eq(1)->text();
                $category = trim($category);

                $title = $node->filter('h2')->eq(0)->text();
                $titleArr = explode('->', $title);
                $title = trim(end($titleArr));

                $time = $node->filter('.pic_fx span')->text();
                $time = strtotime(str_replace('发布时间：', '', $time));

                $contentNode = $node->filter('.pic_pview ul');
                $this->content = $contentNode->html();
//echo $content."\n";die;
                $img = $node->filter('li img');
                $img->each(function ($img_node){
                    $src = $img_node->attr('src');
                    $url = $this->baseUrl . '/'.ltrim($src, '/');
                    $ossInfo = $this->uploadOss($url);
                    if($ossInfo){
                        $this->content = str_replace($src, '{{'.$ossInfo['md5'].'}}', $this->content);
                        $this->ossInfo[] = $ossInfo;
                    }
                });

                if($category && $title && $time && $this->content){
                    return json_encode(['category'=>$category,'title'=>$title,'content'=>$this->content,'oss'=>$this->ossInfo,'time'=>$time, 'source'=>$this->name, 'author'=>isset($name) ? $name : $this->name]);
                }
            }catch (\Exception $e){
                $this->addLog($url, 'log', false, $e->getMessage());
            }
        }
        return '';
    }

    public function uploadOss($url)
    {
        $bucket = \Yii::$app->params['oss']['image']['bucket'];
        $ossClient = Aliyunoss::getOssClient();
        if($filePath = self::get_file($url, '/tmp/joke')){
            $mimeType = FileHelper::getMimeType($filePath);
            $ext = explode('/', $mimeType)[1];
            $md5File = md5_file($filePath);
            if(ArticleEntity::find()->where(['md5'=>$md5File])->exists()){
                return [];
            }
            $ossPathEntity = $this->ossDir . $md5File.'.'.$ext;
            $ossClient->uploadFile($bucket, $ossPathEntity, $filePath);
            return [
                'md5' =>$md5File,
                'mime' =>$mimeType,
                'entity' =>$ossPathEntity,
            ];
        }else{
            return [];
        }
    }

    public static function get_file($url, $folder = "./") {
        $destination_folder = $folder . '/'; // 文件下载保存目录，默认为当前文件目录
        if (!is_dir($destination_folder)) { // 判断目录是否存在
            FileHelper::createDirectory($destination_folder);// 如果没有就建立目录
        }
        $newfname = $destination_folder . basename($url); // 取得文件的名称
        $file = fopen ($url, "rb"); // 远程下载文件，二进制模式
        if ($file) { // 如果下载成功
            $newf = fopen ($newfname, "wb"); // 远在文件文件
            if ($newf) // 如果文件保存成功
                while (!feof($file)) { // 判断附件写入是否完整
                    fwrite($newf, fread($file, 1024 * 8), 1024 * 8); // 没有写完就继续
                }
        }
        if ($file) {
            fclose($file); // 关闭远程文件
        }
        if ($newf) {
            fclose($newf); // 关闭本地文件
        }
        return $newfname;
    }
}