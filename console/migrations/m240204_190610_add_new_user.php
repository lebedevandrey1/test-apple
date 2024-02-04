<?php

use yii\db\Migration;

/**
 * Добавление пользователя в систему
 */
class m240204_190610_add_new_user extends Migration
{
    public function safeUp()
    {
        $this->execute(<<<SQL
            INSERT INTO `user` (`id`, `username`, `auth_key`, `password_hash`, `password_reset_token`, `email`, `status`, `created_at`, `updated_at`, `verification_token`) VALUES
            (1, 'admin', 'ZmsZH7zYaeQx7p2cvBUDtZrN21X3QT9R', '$2y$13\$Ij/J/C4A5QzP4z9gqt4MaO9zdN3NZrYJC8wc5fMVn0PSRzb8llXz.', NULL, 'mail@mail.ru', 10, 1707046065, 1707046065, 'Svg0XOczGEWiGgOhV7yMrWjRu22KQUk7_1707046065');
        SQL);
    }

    public function safeDown()
    {
        $this->truncateTable('user');
    }
}
