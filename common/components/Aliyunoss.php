<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/5
 * Time: 15:49
 */

namespace common\components;

use OSS\Core\OssException;
use OSS\OssClient;
use Yii;

class Aliyunoss
{
    /**
     * 根据Config配置，得到一个OssClient实例
     *
     * @return OssClient 一个OssClient实例
     */
    public static function getOssClient($endpoint = '')
    {
        $ossConfig = Yii::$app->params['oss'];
        try {
            $ossClient = new OssClient($ossConfig['accessKeyId'], $ossConfig['accessKeySecret'], $endpoint?:$ossConfig['endPoint'], false);
        } catch (OssException $e) {
            printf(__FUNCTION__ . "creating OssClient instance: FAILED\n");
            printf($e->getMessage() . "\n");
            return null;
        }
        return $ossClient;
    }
}