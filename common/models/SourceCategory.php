<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "source_category".
 *
 * @property string $id
 * @property string $source_id
 * @property string $category_id
 * @property string $category_url 收集网页地址
 * @property string $name 收集描述
 * @property string $max_ctime 收集最大发布时间
 * @property string $created_at
 */
class SourceCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'source_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['source_id', 'category_id', 'max_ctime', 'created_at'], 'required'],
            [['source_id', 'category_id', 'max_ctime', 'created_at'], 'integer'],
            [['category_url', 'name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'source_id' => 'Source ID',
            'category_id' => 'Category ID',
            'category_url' => 'Category Url',
            'name' => 'Name',
            'max_ctime' => 'Max Ctime',
            'created_at' => 'Created At',
        ];
    }
}
