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
        echo $this->args['name'];
    }
}