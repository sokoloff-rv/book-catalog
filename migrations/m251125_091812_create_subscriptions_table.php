<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%subscriptions}}`.
 */
class m251125_091812_create_subscriptions_table extends Migration
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

        $this->createTable('{{%subscriptions}}', [
            'id' => $this->bigPrimaryKey(),
            'author_id' => $this->bigInteger()->notNull(),
            'phone' => $this->string(32)->notNull(),
            'user_id' => $this->bigInteger(),
            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex(
            'idx_subscriptions_author_id',
            '{{%subscriptions}}',
            'author_id'
        );

        $this->createIndex(
            'idx_subscriptions_user_id',
            '{{%subscriptions}}',
            'user_id'
        );

        $this->addForeignKey(
            'fk_subscriptions_author_id',
            '{{%subscriptions}}',
            'author_id',
            '{{%authors}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_subscriptions_user_id',
            '{{%subscriptions}}',
            'user_id',
            '{{%users}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_subscriptions_user_id', '{{%subscriptions}}');
        $this->dropForeignKey('fk_subscriptions_author_id', '{{%subscriptions}}');

        $this->dropIndex('idx_subscriptions_user_id', '{{%subscriptions}}');
        $this->dropIndex('idx_subscriptions_author_id', '{{%subscriptions}}');

        $this->dropTable('{{%subscriptions}}');
    }
}
