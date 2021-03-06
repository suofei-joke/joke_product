<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "article".
 *
 * @property string $id
 * @property string $title 文章标题
 * @property string $content 文章详情
 * @property string $url 文章链接
 * @property string $author 作者
 * @property int $type 类型 0-纯文本 1-有图片 2-有视频
 * @property string $tag_id
 * @property int $status 文章状态
 * @property string $published_at
 */
class Article extends \yii\db\ActiveRecord
{
    const STATUS_GATHER = 1;

    public static $ARTICLE_TYPE = [
        'article' => 0,
        'image' => 1,
        'video' => 2,
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'article';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['content', 'status', 'published_at'], 'required'],
            [['content'], 'string'],
            [['tag_id', 'type', 'status', 'published_at'], 'integer'],
            [['title', 'url', 'author'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'content' => 'Content',
            'url' => 'Url',
            'tag_id' => 'Tag ID',
            'author' => 'Author',
            'type' => 'Type',
            'status' => 'Status',
            'published_at' => 'Published At',
        ];
    }
}
