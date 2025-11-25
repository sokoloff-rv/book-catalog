<?php

namespace app\models;

use Yii;
use yii\helpers\Url;

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
 * @property int[] $authorIds
 * @property SmsLog[] $smsLogs
 */
class Book extends \yii\db\ActiveRecord
{

    /** @var int[] List of IDs of selected authors. */
    public array $authorIds = [];

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
            ['authorIds', 'each', 'rule' => ['integer']],
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
            'authorIds' => 'Авторы',
        ];
    }

    /**
     * Gets query for [[Author]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthors()
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

    /**
     * Synchronizes selected authors with the relations table.
     */
    protected function syncAuthors(): void
    {
        $newAuthorIds = array_unique(array_map('intval', $this->authorIds));
        sort($newAuthorIds);

        $currentAuthorIds = $this->getBookAuthor()->select('author_id')->column();
        sort($currentAuthorIds);

        $toAdd = array_diff($newAuthorIds, $currentAuthorIds);
        $toRemove = array_diff($currentAuthorIds, $newAuthorIds);

        if ($toRemove) {
            BookAuthor::deleteAll([
                'book_id' => $this->id,
                'author_id' => $toRemove,
            ]);
        }

        foreach ($toAdd as $authorId) {
            $link = new BookAuthor([
                'book_id' => $this->id,
                'author_id' => $authorId,
            ]);
            $link->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $this->syncAuthors();
    }

    /**
     * {@inheritdoc}
     */
    public function afterFind()
    {
        parent::afterFind();
        $this->authorIds = $this->getAuthors()->select('id')->column();
    }

    /**
     * Returns the full URL of the book cover.
     *
     * @return string|null
     */
    public function getCoverUrl(): ?string
    {
        if (!$this->cover_path) {
            return null;
        }

        // for external files
        if (preg_match('~^(https?:)?//~', $this->cover_path)) {
            return $this->cover_path;
        }

        $relativePath = ltrim($this->cover_path, '/');

        return Url::to("@web/{$relativePath}");
    }
}
