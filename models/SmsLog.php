<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sms_logs".
 *
 * @property int $id
 * @property int $subscription_id
 * @property int $book_id
 * @property int $sent_at
 * @property string|null $status
 * @property string|null $provider_raw
 *
 * @property Book $book
 * @property Subscription $subscription
 */
class SmsLog extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sms_logs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'provider_raw'], 'default', 'value' => null],
            [['subscription_id', 'book_id', 'sent_at'], 'required'],
            [['subscription_id', 'book_id', 'sent_at'], 'integer'],
            [['provider_raw'], 'string'],
            [['status'], 'string', 'max' => 32],
            [['book_id'], 'exist', 'skipOnError' => true, 'targetClass' => Book::class, 'targetAttribute' => ['book_id' => 'id']],
            [['subscription_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subscription::class, 'targetAttribute' => ['subscription_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'subscription_id' => 'Subscription ID',
            'book_id' => 'Book ID',
            'sent_at' => 'Sent At',
            'status' => 'Status',
            'provider_raw' => 'Provider Raw',
        ];
    }

    /**
     * Gets query for [[Book]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBook()
    {
        return $this->hasOne(Book::class, ['id' => 'book_id']);
    }

    /**
     * Gets query for [[Subscription]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubscription()
    {
        return $this->hasOne(Subscription::class, ['id' => 'subscription_id']);
    }

}
