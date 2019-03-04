<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "source".
 *
 * @property string $id
 * @property string $name 名称
 * @property string $base_url 来源主网址
 * @property string $created_at
 */
class Source extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'source';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at'], 'required'],
            [['created_at'], 'integer'],
            [['name', 'base_url'], 'string', 'max' => 255],
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
            'base_url' => 'Base Url',
            'created_at' => 'Created At',
        ];
    }

    public function getCategory()
    {
        return $this->hasMany(SourceCategory::className(), ['source_id'=>'id']);
    }
}
