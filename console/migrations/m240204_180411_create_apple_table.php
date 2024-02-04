<?php

use yii\db\Migration;

/**
 * Создание таблицы apple
 */
class m240204_180411_create_apple_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%apple}}', [
            'id' => $this->integer(11)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'title' => $this->string(100)->null()->comment('Наименование'),
            'color' => $this->tinyInteger(1)
                ->notNull()
                ->defaultValue(1)
                ->comment('Цвет (1 - красный, 2 - зеленый, 3 - красно-зеленый)'),
            'status' => $this->tinyInteger(1)
                ->notNull()
                ->defaultValue(0)
                ->comment('Статус (0 - на дереве, 1 - упало)'),
            'eat_part' => $this->integer(3)
                ->notNull()
                ->defaultValue(100)
                ->comment('Какая часть яблока осталась (в %, без дробей)'),
            'created_at' => $this->dateTime()->null()->comment('Дата появления'),
            'dropped_at' => $this->dateTime()->null()->comment('Дата падения'),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%apple}}');
    }
}
