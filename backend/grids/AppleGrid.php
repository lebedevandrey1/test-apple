<?php

declare(strict_types=1);

namespace backend\grids;

use backend\traits\DefaultGridTrait;
use common\models\Apple;

/**
* Таблица данных {{ apple }}
*/
class AppleGrid extends Apple
{
    use DefaultGridTrait;

    public $condition;


    /**
    * @var string Поле, по которому происходит дефолтная сортировка
    */
    protected string $defaultSortField = 'id';
    /**
     * @var int Дефолтный порядок сортировки (Напр: SORT_DESC или SORT_ASC)
     */
    protected int $defaultSortOrder = SORT_ASC;


    public function __construct(string $repository, array $linkedTables = [])
    {
        $this->initGrid($repository, $linkedTables);

        parent::__construct();
    }

    public function rules(): array
    {
        return [

            ['id', 'safe'],

            ['title', 'safe'],

            ['color', 'safe'],

            ['status', 'safe'],

            ['eat_part', 'safe'],

            ['created_at', 'safe'],

            ['dropped_at', 'safe'],

        ];
    }

    public function attributeLabels(): array
    {
        return [
            'title' => 'Наименование',
            'color' => 'Цвет',
            'status' => 'Статус',
            'eat_part' => 'Осталось',
            'created_at' => 'Дата появления',
            'dropped_at' => 'Дата падения'
        ];
    }

    /**
    * Массив полей, по которым можно делать сортировку
    */
    protected function getSortableFields(): array
    {
        return [
            'id',
            'title',
            'color',
            'status',
            'eat_part',
            'created_at',
            'dropped_at',
        ];
    }

    /**
     * Массив полей, по которым можно делать поиск
     */
    protected function getSearchableFields(): array
    {
        return [
            'id' => Apple::tableName() . '.id',
            'title' => Apple::tableName() . '.title',
            'color' => Apple::tableName() . '.color',
            'status' => Apple::tableName() . '.status',
            'eat_part' => Apple::tableName() . '.eat_part',
            'created_at' => Apple::tableName() . '.created_at',
            'dropped_at' => Apple::tableName() . '.dropped_at',
        ];
    }

    public function formName()
    {
        return 'filter';
    }
}
