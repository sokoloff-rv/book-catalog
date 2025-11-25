<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Seeds base RBAC roles and permissions.
 */
class m251125_112256_seed_rbac_roles extends Migration
{
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $manageBooks = $auth->createPermission('manageBooks');
        $manageBooks->description = 'Добавление, редактирование и удаление книг';
        $auth->add($manageBooks);

        $manageAuthors = $auth->createPermission('manageAuthors');
        $manageAuthors->description = 'Добавление, редактирование и удаление авторов';
        $auth->add($manageAuthors);

        $userRole = $auth->createRole('user');
        $userRole->description = 'Авторизованный пользователь каталога';
        $auth->add($userRole);
        $auth->addChild($userRole, $manageBooks);
        $auth->addChild($userRole, $manageAuthors);

        $userIds = (new Query())
            ->from('{{%users}}')
            ->select('id')
            ->column($this->db);

        foreach ($userIds as $userId) {
            $auth->assign($userRole, $userId);
        }
    }

    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        if ($role = $auth->getRole('user')) {
            $auth->remove($role);
        }

        if ($permission = $auth->getPermission('manageBooks')) {
            $auth->remove($permission);
        }

        if ($permission = $auth->getPermission('manageAuthors')) {
            $auth->remove($permission);
        }
    }
}
