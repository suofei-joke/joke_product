<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "article_entity".
 *
 * @property string $id
 * @property string $md5 文件md5值
 * @property string $mime 文件名
 * @property string $entity oss对应的详细地址
 */
class ArticleEntity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'article_entity';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['md5', 'mime', 'entity'], 'required'],
            [['md5', 'mime', 'entity'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'md5' => 'Md5',
            'mime' => 'Mime',
            'entity' => 'Entity',
        ];
    }
}
