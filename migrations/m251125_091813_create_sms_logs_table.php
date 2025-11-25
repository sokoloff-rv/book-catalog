<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sms_logs}}`.
 */
class m251125_091813_create_sms_logs_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        /** @var \yii\db\Connection $db */
        $db = $this->db;
        $tableOptions = null;
        if ($db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%sms_logs}}', [
            'id' => $this->bigPrimaryKey(),
            'subscription_id' => $this->bigInteger()->notNull(),
            'book_id' => $this->bigInteger()->notNull(),
            'sent_at' => $this->integer()->notNull(),
            'status' => $this->string(32),
            'provider_raw' => $this->text(),
        ], $tableOptions);

        $this->createIndex(
            'idx_sms_logs_subscription_id',
            '{{%sms_logs}}',
            'subscription_id'
        );

        $this->createIndex(
            'idx_sms_logs_book_id',
            '{{%sms_logs}}',
            'book_id'
        );

        $this->addForeignKey(
            'fk_sms_logs_subscription_id',
            '{{%sms_logs}}',
            'subscription_id',
            '{{%subscriptions}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_sms_logs_book_id',
            '{{%sms_logs}}',
            'book_id',
            '{{%books}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_sms_logs_book_id', '{{%sms_logs}}');
        $this->dropForeignKey('fk_sms_logs_subscription_id', '{{%sms_logs}}');

        $this->dropIndex('idx_sms_logs_book_id', '{{%sms_logs}}');
        $this->dropIndex('idx_sms_logs_subscription_id', '{{%sms_logs}}');

        $this->dropTable('{{%sms_logs}}');
    }
}
