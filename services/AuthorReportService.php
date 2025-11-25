<?php

namespace app\services;

use Yii;
use yii\db\Connection;
use yii\db\Query;

class AuthorReportService
{
    public function __construct(private ?Connection $db = null)
    {
    }

    public function getTopAuthorsByYear(int $year, int $limit = 10): array
    {
        $db = $this->db ?? Yii::$app->db;

        $query = (new Query())
            ->select([
                'author_id' => 'a.id',
                'full_name' => 'a.full_name',
                'book_count' => 'COUNT(b.id)',
            ])
            ->from(['a' => 'authors'])
            ->innerJoin(['ba' => 'book_authors'], 'ba.author_id = a.id')
            ->innerJoin(['b' => 'books'], 'b.id = ba.book_id')
            ->where(['b.publish_year' => $year])
            ->groupBy(['a.id', 'a.full_name'])
            ->orderBy(['book_count' => SORT_DESC, 'full_name' => SORT_ASC])
            ->limit($limit);

        $rows = $query->all($db);

        return array_map(static function (array $row): array {
            $row['book_count'] = (int) $row['book_count'];

            return $row;
        }, $rows);
    }
}
