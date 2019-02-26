<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tag".
 *
 * @property string $id
 * @property string $name 标签名
 * @property string $article_count
 */
class Tag extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tag';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'article_count'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'article_count' => 'Article Count',
        ];
    }
}
