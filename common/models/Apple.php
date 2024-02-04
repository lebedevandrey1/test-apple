<?php

declare(strict_types=1);

namespace common\models;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string|null $title Наименование
 * @property string|null $color Цвет
 * @property int $status Статус (0 - на дереве, 1 - упало)
 * @property int $eat_part Какая часть яблока осталась (в %, без дробей)
 * @property string|null $created_at Дата появления
 * @property string|null $dropped_at Дата падения
 */
class Apple extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%apple}}';
    }
}
