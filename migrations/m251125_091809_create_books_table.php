<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%books}}`.
 */
class m251125_091809_create_books_table extends Migration
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

        $this->createTable('{{%books}}', [
            'id' => $this->bigPrimaryKey(),
            'title' => $this->string(255)->notNull(),
            'publish_year' => $this->smallInteger()->notNull(),
            'description' => $this->text(),
            'isbn' => $this->string(32),
            'cover_path' => $this->string(255),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%books}}');
    }
}
