<?php

declare(strict_types=1);

namespace app\commands;

use app\models\Author;
use app\models\Book;
use app\models\BookAuthor;
use Faker\Factory;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\db\Exception;

class SeedController extends Controller
{
    public $defaultAction = 'books';

    /**
     * Заполняет базу тестовыми авторами и книгами.
     */
    public function actionBooks(int $bookCount = 1000): int
    {
        $faker = Factory::create();
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();

        try {
            BookAuthor::deleteAll();
            Book::deleteAll();
            Author::deleteAll();

            $now = time();
            $authorCount = max(30, (int)floor($bookCount / 10));
            $authorIds = $this->createAuthors($faker, $authorCount, $now);
            $createdBooks = $this->createBooks($faker, $bookCount, $authorIds, $now);

            $transaction->commit();
            $this->stdout("Создано авторов: {$authorCount}\n");
            $this->stdout("Создано книг: {$createdBooks}\n");

            return ExitCode::OK;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * @return int[]
     * @throws Exception
     */
    private function createAuthors(\Faker\Generator $faker, int $authorCount, int $now): array
    {
        $authorIds = [];

        for ($i = 0; $i < $authorCount; $i++) {
            $createdAt = $now - $faker->numberBetween(0, 365 * 86400);
            $updatedAt = $createdAt + $faker->numberBetween(0, 30 * 86400);

            $author = new Author([
                'full_name' => $faker->name(),
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ]);

            if (!$author->save(false)) {
                throw new Exception('Не удалось сохранить автора.');
            }

            $authorIds[] = (int)$author->id;
        }

        return $authorIds;
    }

    /**
     * @param int[] $authorIds
     * @return int
     * @throws Exception
     */
    private function createBooks(\Faker\Generator $faker, int $bookCount, array $authorIds, int $now): int
    {
        $createdBooks = 0;

        for ($i = 0; $i < $bookCount; $i++) {
            $createdAt = $now - $faker->numberBetween(0, 365 * 86400);
            $updatedAt = $createdAt + $faker->numberBetween(0, 30 * 86400);

            $book = new Book([
                'title' => $faker->sentence(3),
                'publish_year' => $faker->numberBetween(1950, (int)date('Y')),
                'description' => $faker->realText(400),
                'isbn' => $faker->isbn13(),
                'cover_path' => null,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ]);

            if (!$book->save(false)) {
                throw new Exception('Не удалось сохранить книгу.');
            }

            $book->cover_path = "image/cover?seed={$book->id}";
            $book->save(false);

            $authorsForBook = $faker->randomElements($authorIds, $faker->numberBetween(1, 3));
            foreach ($authorsForBook as $authorId) {
                $bookAuthor = new BookAuthor([
                    'book_id' => (int)$book->id,
                    'author_id' => $authorId,
                ]);

                if (!$bookAuthor->save(false)) {
                    throw new Exception('Не удалось связать книгу с автором.');
                }
            }

            $createdBooks++;
        }

        return $createdBooks;
    }
}
