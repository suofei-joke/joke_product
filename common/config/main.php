<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=121.42.242.167;dbname=joke',
            'username' => 'root',
            'password' => '123456ljx',
            'charset' => 'utf8mb4',
        ],
        'Aliyunoss' => [
            'class' => 'app\components\Aliyunoss',
        ],
    ],
];
