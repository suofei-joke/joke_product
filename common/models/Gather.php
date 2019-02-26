<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "gather".
 *
 * @property string $id
 * @property string $name
 * @property string $category
 * @property string $url 抓取链接
 * @property string $url_org
 * @property int $res
 * @property string $result
 */
class Gather extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gather';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category', 'url', 'url_org', 'result'], 'required'],
            [['res'], 'integer'],
            [['name', 'category', 'url', 'url_org', 'result'], 'string', 'max' => 255],
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
            'category' => 'Category',
            'url' => 'Url',
            'url_org' => 'Url Org',
            'res' => 'Res',
            'result' => 'Result',
        ];
    }
}
