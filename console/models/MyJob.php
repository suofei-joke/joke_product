<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/27
 * Time: 10:06
 */

namespace console\models;


class MyJob
{
    public function perform()
    {
        file_put_contents('/tmp/ljx.log', json_encode($this->args). "\n", FILE_APPEND);
        echo $this->args['name'];
    }
}