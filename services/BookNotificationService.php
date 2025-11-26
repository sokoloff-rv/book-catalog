<?php

namespace app\services;

use app\models\Author;
use app\models\Book;
use app\models\SmsLog;
use app\models\Subscription;
use Yii;

class BookNotificationService implements BookNotificationServiceInterface
{
    public function __construct(private readonly SmsServiceInterface $smsService)
    {
    }

    public function notify(Book $book): void
    {
        $authorIds = $this->resolveAuthorIds($book);
        if (!$authorIds) {
            return;
        }

        $subscriptions = $this->findSubscriptions($authorIds);
        if (!$subscriptions) {
            return;
        }

        $message = $this->buildMessage($book, $authorIds);

        foreach ($subscriptions as $subscription) {
            $result = $this->sendNotification($subscription->phone, $message);
            $this->logResult($subscription->id, $book->id, $result);
        }
    }

    /**
     * @return int[]
     */
    private function resolveAuthorIds(Book $book): array
    {
        if ($book->authorIds) {
            return $book->authorIds;
        }

        return $book->getAuthors()->select('id')->column();
    }

    /**
     * @param int[] $authorIds
     * @return Subscription[]
     */
    private function findSubscriptions(array $authorIds): array
    {
        return Subscription::find()
            ->where(['author_id' => $authorIds])
            ->all();
    }

    /**
     * @param int[] $authorIds
     */
    private function buildMessage(Book $book, array $authorIds): string
    {
        $authorNames = Author::find()
            ->select('full_name')
            ->where(['id' => $authorIds])
            ->column();

        return sprintf(
            'Новая книга "%s" (%s). Авторы: %s.',
            $book->title,
            $book->publish_year,
            $authorNames ? implode(', ', $authorNames) : 'неизвестно'
        );
    }

    private function sendNotification(string $phone, string $message): SmsResult
    {
        try {
            return $this->smsService->send($phone, $message);
        } catch (\Throwable $exception) {
            return new SmsResult(false, 'exception', $exception->getMessage());
        }
    }

    private function logResult(int $subscriptionId, int $bookId, SmsResult $result): void
    {
        $log = new SmsLog([
            'subscription_id' => $subscriptionId,
            'book_id' => $bookId,
            'sent_at' => time(),
            'status' => $result->status,
            'provider_raw' => $result->raw,
        ]);

        if (!$log->save()) {
            Yii::error('Не удалось сохранить лог SMS: ' . json_encode($log->errors));
        }
    }
}
