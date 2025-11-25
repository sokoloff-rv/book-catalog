<?php

namespace app\models;

use Yii;
use yii\base\Model;

class SignupForm extends Model
{
    public string $username = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';

    public function rules(): array
    {
        return [
            [['username', 'password'], 'required'],
            [['username', 'email'], 'trim'],
            ['email', 'email'],
            [['username'], 'string', 'min' => 3, 'max' => 191],
            [['password'], 'string', 'min' => 6],
            ['phone', 'string', 'max' => 32],
            ['username', 'unique', 'targetClass' => User::class, 'message' => 'Этот логин уже занят.'],
            ['email', 'unique', 'targetClass' => User::class, 'message' => 'Этот email уже используется.'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'username' => 'Логин',
            'email' => 'Email',
            'phone' => 'Телефон',
            'password' => 'Пароль',
        ];
    }

    public function signup(): ?User
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email ?: null;
        $user->phone = $this->phone ?: null;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->status = User::STATUS_ACTIVE;

        if (!$user->save()) {
            return null;
        }

        if (($role = Yii::$app->authManager->getRole('user')) !== null) {
            Yii::$app->authManager->assign($role, $user->id);
        }

        return $user;
    }
}
