<?php

declare(strict_types=1);

namespace backend\repositories;

use backend\scopes\BaseScope;
use backend\scopes\Scope;
use yii\base\Model;
use yii\db\ActiveQueryInterface;
use yii\db\mssql\PDO;

/**
* Базовый репозиторий общих методов
*/
abstract class BaseRepository
{
    /**
    * Модель для работы с репозиторием
    */
    abstract protected function getClass(): string;
    /**
     * Скоуп для работы с запросами к БД
     */
    abstract public function scope(): Scope;


    /**
    * Проксирование запросов к find через query
    *
    * @return ActiveQueryInterface
    */
    protected function query(): ActiveQueryInterface
    {
        return forward_static_call([$this->getClass(), 'find']);
    }

    /**
    * Проксирование конечных запросов к БД
    *
    * @param BaseScope $scope Объект Scope. Напр: one()
    * @param string|null $fetchDTOClass
    */
    public function execute(BaseScope $scope, ?string $fetchDTOClass = null): mixed
    {
        if ($scope->getResultType() === BaseScope::RESULT_TYPE_COUNT) {
            return (int) $scope->getQuery()->count(); // Query::count() возвращает строку
        }
        if ($fetchDTOClass && class_exists($fetchDTOClass)) {
            $command = $scope->getQuery()->createCommand();
            return call_user_func(
                [$command, 'query' . ucfirst($scope->getResultType())],
                [PDO::FETCH_CLASS, $fetchDTOClass]
            );
        } else {
            return call_user_func([$scope->getQuery(), $scope->getResultType()]);
        }
    }

    /**
     * Пакетная вставка данных в БД
     *
     * @param string $tableName Наименование таблицы
     * @param array $columns Колонки
     * @param array $values Значения
     * @param int $chunkSize Максимальный размер пакета
     */
    public function batchInsert(string $tableName, array $columns, array $values, int $chunkSize = 5000): void
    {
        $chunks = array_chunk($values, $chunkSize);
        $class = $this->getClass();
        $db = $class::getDb();

        foreach ($chunks as $chunk) {
            $db->createCommand()
                ->batchInsert($tableName, $columns, $chunk)
                ->execute();
        }
    }

    /**
     * Сохранение данных
     *
     * @param Model $record Объект модели для сохранения
     */
    public function save(Model $record): bool
    {
        return $record->save(false);
    }

    /**
     * Выборка данных в презентер
     *
     * @param BaseScope $scope
     */
    public function getCollectionByScope(BaseScope $scope): array
    {
        if (isset($paginator)) {
            $limit = $paginator->limit();
            $paginator->setTotal($this->execute($scope->count()));

            if ($limit > 0) {
                $scope->setLimit($limit);
            }
        }

        return $this->execute($scope->all());
    }
}
