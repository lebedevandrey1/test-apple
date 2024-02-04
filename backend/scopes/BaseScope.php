<?php

declare(strict_types=1);

namespace backend\scopes;

use InvalidArgumentException;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecordInterface;

abstract class BaseScope
{
    public const RESULT_TYPE_ALL = 'all';
    public const RESULT_TYPE_ONE = 'one';
    public const RESULT_TYPE_COUNT = 'count';

    /**
     * @var ActiveQueryInterface
     */
    public $query;

    /**
     * @var string
     */
    private $resultType;

    /**
     * @var string
     */
    protected $className;

    /**
     * @var string
     */
    protected $alias;


    public function __construct(string $className)
    {
        if (!$className || !is_subclass_of($className, ActiveRecordInterface::class)) {
            throw new InvalidArgumentException(
                'Треубется объект ActiveRecord'
            );
        }
        $this->className = $className;
        $this->query = call_user_func(
            [
                $this->getClass(),
                'find'
            ]
        );
        $this->resultType = static::RESULT_TYPE_ALL;
        $tableMethodName = $this->getTableMethodName();
        $this->alias = $this->className::$tableMethodName();
    }

    public function byId(int $id): BaseScope
    {
        $this->getQuery()->andWhere([$this->alias . '.id' => $id]);

        return $this;
    }

    public function orderByArray(array $order): BaseScope
    {
        $this->getQuery()->orderBy($order);

        return $this;
    }

    protected function getClass(): string
    {
        return $this->className;
    }

    public function getQuery(): ActiveQueryInterface
    {
        return $this->query;
    }

    public function all(): BaseScope
    {
        $this->resultType = static::RESULT_TYPE_ALL;

        return $this;
    }

    public function one(): BaseScope
    {
        $this->resultType = static::RESULT_TYPE_ONE;

        return $this;
    }

    final public function getResultType(): string
    {
        return $this->resultType;
    }

    public function asArray(): BaseScope
    {
        $this->getQuery()->asArray();

        return $this;
    }

    public function getTableMethodName(): string
    {
        return 'tableName';
    }

    public function setLimit(int $limit): BaseScope
    {
        $this->getQuery()->limit($limit);

        return $this;
    }

    public function count(): BaseScope
    {
        $this->resultType = static::RESULT_TYPE_COUNT;

        return $this;
    }

    public function andFilterWhereCondition($condition): BaseScope
    {
        $this->getQuery()->andFilterWhere($condition);

        return $this;
    }

    public function orFilterWhereCondition(array $condition): BaseScope
    {
        $this->getQuery()->orFilterWhere($condition);

        return $this;
    }
}
