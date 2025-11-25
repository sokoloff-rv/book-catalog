<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%book_authors}}`.
 */
class m251125_091811_create_book_authors_table extends Migration
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

        $this->createTable('{{%book_authors}}', [
            'book_id' => $this->bigInteger()->notNull(),
            'author_id' => $this->bigInteger()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey(
            'pk_book_authors',
            '{{%book_authors}}',
            ['book_id', 'author_id']
        );

        $this->createIndex(
            'idx_book_authors_book_id',
            '{{%book_authors}}',
            'book_id'
        );

        $this->createIndex(
            'idx_book_authors_author_id',
            '{{%book_authors}}',
            'author_id'
        );

        $this->addForeignKey(
            'fk_book_authors_book_id',
            '{{%book_authors}}',
            'book_id',
            '{{%books}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_book_authors_author_id',
            '{{%book_authors}}',
            'author_id',
            '{{%authors}}',
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
        $this->dropForeignKey('fk_book_authors_author_id', '{{%book_authors}}');
        $this->dropForeignKey('fk_book_authors_book_id', '{{%book_authors}}');

        $this->dropIndex('idx_book_authors_author_id', '{{%book_authors}}');
        $this->dropIndex('idx_book_authors_book_id', '{{%book_authors}}');

        $this->dropPrimaryKey('pk_book_authors', '{{%book_authors}}');

        $this->dropTable('{{%book_authors}}');
    }
}
