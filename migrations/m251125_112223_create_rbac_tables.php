<?php

use yii\db\Migration;

/**
 * Creates basic RBAC tables using the default Yii2 schema.
 */
class m251125_112223_create_rbac_tables extends Migration
{
    public function safeUp()
    {
        /** @var \yii\db\Connection $db */
        $db = $this->db;
        $tableOptions = null;
        if ($db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%auth_rule}}', [
            'name' => $this->string(64)->notNull(),
            'data' => $this->binary(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'PRIMARY KEY(name)',
        ], $tableOptions);

        $this->createTable('{{%auth_item}}', [
            'name' => $this->string(64)->notNull(),
            'type' => $this->smallInteger()->notNull(),
            'description' => $this->text(),
            'rule_name' => $this->string(64),
            'data' => $this->binary(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'PRIMARY KEY(name)',
        ], $tableOptions);

        $this->createIndex('idx-auth_item-type', '{{%auth_item}}', 'type');
        $this->addForeignKey(
            'fk-auth_item-rule_name',
            '{{%auth_item}}',
            'rule_name',
            '{{%auth_rule}}',
            'name',
            'SET NULL',
            'CASCADE'
        );

        $this->createTable('{{%auth_item_child}}', [
            'parent' => $this->string(64)->notNull(),
            'child' => $this->string(64)->notNull(),
            'PRIMARY KEY(parent, child)',
        ], $tableOptions);

        $this->addForeignKey(
            'fk-auth_item_child-parent',
            '{{%auth_item_child}}',
            'parent',
            '{{%auth_item}}',
            'name',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-auth_item_child-child',
            '{{%auth_item_child}}',
            'child',
            '{{%auth_item}}',
            'name',
            'CASCADE',
            'CASCADE'
        );

        $this->createTable('{{%auth_assignment}}', [
            'item_name' => $this->string(64)->notNull(),
            'user_id' => $this->bigInteger()->notNull(),
            'created_at' => $this->integer(),
            'PRIMARY KEY(item_name, user_id)',
        ], $tableOptions);

        $this->addForeignKey(
            'fk-auth_assignment-item_name',
            '{{%auth_assignment}}',
            'item_name',
            '{{%auth_item}}',
            'name',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-auth_assignment-item_name', '{{%auth_assignment}}');
        $this->dropTable('{{%auth_assignment}}');

        $this->dropForeignKey('fk-auth_item_child-child', '{{%auth_item_child}}');
        $this->dropForeignKey('fk-auth_item_child-parent', '{{%auth_item_child}}');
        $this->dropTable('{{%auth_item_child}}');

        $this->dropForeignKey('fk-auth_item-rule_name', '{{%auth_item}}');
        $this->dropIndex('idx-auth_item-type', '{{%auth_item}}');
        $this->dropTable('{{%auth_item}}');

        $this->dropTable('{{%auth_rule}}');
    }
}
