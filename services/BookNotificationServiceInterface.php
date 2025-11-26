<?php

namespace app\services;

use app\models\Book;

interface BookNotificationServiceInterface
{
    public function notify(Book $book): void;
}
