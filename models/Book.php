<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "books".
 *
 * @property int $id
 * @property string $title
 * @property int $publish_year
 * @property string|null $description
 * @property string|null $isbn
 * @property string|null $cover_path
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Author[] $authors
 * @property BookAuthor[] $BookAuthor
 * @property SmsLog[] $smsLogs
 */
class Book extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'books';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description', 'isbn', 'cover_path'], 'default', 'value' => null],
            [['title', 'publish_year', 'created_at', 'updated_at'], 'required'],
            [['publish_year', 'created_at', 'updated_at'], 'integer'],
            [['description'], 'string'],
            [['title', 'cover_path'], 'string', 'max' => 255],
            [['isbn'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название',
            'publish_year' => 'Год издания',
            'description' => 'Описание',
            'isbn' => 'ISBN',
            'cover_path' => 'Ссылка на обложку',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }

    /**
     * Gets query for [[Author]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasMany(Author::class, ['id' => 'author_id'])->viaTable('book_authors', ['book_id' => 'id']);
    }

    /**
     * Gets query for [[BookAuthor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBookAuthor()
    {
        return $this->hasMany(BookAuthor::class, ['book_id' => 'id']);
    }

    /**
     * Gets query for [[SmsLog]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSmsLog()
    {
        return $this->hasMany(SmsLog::class, ['book_id' => 'id']);
    }

}
