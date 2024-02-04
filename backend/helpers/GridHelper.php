<?php

declare(strict_types=1);

namespace backend\helpers;

/**
 * Помощник для работы с табличными данными
 */
class GridHelper
{
    /**
     * @var string Разделитель слов при поиске
     */
    private string $wordSeparator = ',';
    /**
     * @var array Доступные цвета
     */
    private array $colors = [
        1 => 'Красный',
        2 => 'Зеленый',
        3 => 'Красно-зеленый'
    ];
    /**
     * @var array Статус
     */
    private array $status = [
        0 => 'На дереве',
        1 => 'Упало',
    ];
    /**
     * @var array Состояние
     */
    private array $condition = [
        0 => 'Съедобное, но надо сорвать',
        1 => 'Съедобное, можно есть',
        2 => 'Гнилое, есть нельзя',
    ];


    /**
     * Подготовка запросов к БД для фильтрации данных
     *
     * @param array $filters Массив данных с фильтрами
     * @param array $searchableFields Массив полей getSearchableFields, по которым может происходить поиск.
     * Например: в admin/grids/AssetTypeGrid
     */
    public function prepareFilters(array $filters, array $searchableFields): array
    {
        $searchArray = [];

        if ($filters) {
            foreach ($filters as $key => $value) {
                if ($value) {
                    // Проверяем, есть ли переданное поле в массиве полей, по которым доступна фильтрация
                    if (array_key_exists($key, $searchableFields)) {
                        // Если значение сразу массив, разделяем через 'or'
                        if (is_array($value)) {
                            $searchArray['and'][$key][] = 'or';
                            foreach ($value as $newValue) {
                                $searchArray['and'][$key][] = ['like', $searchableFields[$key], trim($newValue)];
                            }
                        } elseif (strpos($value, $this->wordSeparator)) {
                            // Если данные переданы через указанный разделитель символов
                            $newValue = explode($this->wordSeparator, $value);
                            $searchArray['and'][$key][] = 'or';
                            foreach ($newValue as $filter) {
                                $searchArray['and'][$key][] = ['like', $searchableFields[$key], trim($filter)];
                            }
                        } else {
                            $searchArray['and'][] = ['like', $searchableFields[$key], trim($value)];
                        }
                    }
                }
            }
        }

        return $searchArray;
    }

    /**
     * Проверка данных из фильтра таблицы на число
     *
     * @param string $values Данные из поля поиска в таблице
     */
    public function checkNumberInStringRule(string $values): bool
    {
        // Проверяем, есть ли в строке разделитель, который указан как разделитель данных при поиске
        if (strpos($values, $this->wordSeparator)) {
            // Если есть, то делим данные по разделителю и проверяем каждое значение на число
            $newValues = explode($this->wordSeparator, $values);
            foreach ($newValues as $value) {
                if (!is_numeric($value)) {
                    return false;
                }
            }
        } elseif (!is_numeric($values)) {
            // Если разделителя нет, значит переданные данные нужно сразу проверить на число
            return false;
        }

        return true;
    }

    public function getColor(int $color): string
    {
        return $this->colors[$color];
    }

    public function getColors(): array
    {
        return $this->colors;
    }

    public function getStatus(int $status): string
    {
        return $this->status[$status];
    }

    public function getStatuses(): array
    {
        return $this->status;
    }

    public function getCondition(array $model)
    {
        $spoilt = date('Y-m-d H:i:s', strtotime("-5 hours"));

        if ($model['status'] === 0) {
            return $this->condition[0];
        }

        if ($model['status'] === 1 && $model['dropped_at'] && $model['dropped_at'] <= $spoilt) {
            return $this->condition[2];
        }

        return $this->condition[1];
    }
}
