<?php

declare(strict_types=1);

namespace backend\traits;

use backend\helpers\GridHelper;
use Yii;
use yii\base\DynamicModel;
use yii\data\ArrayDataProvider;
use yii\web\BadRequestHttpException;

/**
 * Формирование данных для показа в таблицах
 */
trait DefaultGridTrait
{
    /**
    * @var object Репозиторий таблицы данных
    */
    protected object $repository;
    /**
     * @var GridHelper Репозиторий таблицы данных
     */
    protected GridHelper $gridHelper;
    /**
     * @var array Массив с данными для фильтра
     */
    protected array $filter = [];
    /**
     * @var string Как вызывается фильтр из адресной строки
     */
    protected string $filterVar = 'filter';
    /**
     * @var array Массив с данными для сортировки
     */
    protected array $sort = [];
    /**
     * @var array Массив связанных таблиц, которые нужны для сбора данных
     */
    protected array $linkedTables;
    /**
     * @var array Ошибки валидации при сортировке записей
     */
    public $errors;


    /**
    * Обязательный метод. Нужен для полей, по которыми будет проходить сортировка
    */
    abstract protected function getSortableFields(): array;
    /**
     * Обязательный метод. Нужен для полей, по которыми будет проходить поиск
     */
    abstract protected function getSearchableFields(): array;


    /**
     * Инициализация данных таблицы
     *
     * @param string $repository Репозиторий объекта
     * @param array $linkedTables Нужные связи таблиц для выборки данных
     */
    public function initGrid(string $repository, array $linkedTables): void
    {
        // Создание объекта сервиса
        $this->repository = Yii::createObject($repository);
        $this->gridHelper = Yii::createObject(GridHelper::class);
        // Нужные связи таблиц для выборки данных
        $this->linkedTables = $linkedTables;
    }

    /**
     * Инициализация фильтров
     */
    public function setFilter(): void
    {
        $this->filter = $this->gridHelper->prepareFilters(
            \Yii::$app->request->get($this->filterVar, []),
            $this->getSearchableFields()
        );
    }

    /**
     * Инициализация объекта модели
     *
     * @param array|null $params Дополнительные параметры поиска
     */
    public function search(?array $params = null): array
    {
        if ($this->filter) {
            $this->validateFilters();
        }

        $scope = $this->repository->scope();
        $scope->asArray();

        if ($params) {
            if (array_key_exists('join', $params)) {
                $scope->joinWith($params['join']);
            }

            if (array_key_exists('condition', $params)) {
                $scope->andWhere($params['condition']);
            }
        }

        if ($this->filter) {
            foreach ($this->filter as $key => $filter) {
                if ($key == 'or') {
                    foreach ($filter as $value) {
                        $scope->orFilterWhereCondition($value);
                    }
                } else {
                    foreach ($filter as $value) {
                        $scope->andFilterWhereCondition($value);
                    }
                }
            }
        }

        if ($this->sort) {
            $scope->orderByArray($this->sort);
        }

        return $this->repository->getCollectionByScope($scope);
    }

    /**
     * Валидация фильтров
     */
    private function validateFilters(): void
    {
        $fields = array_keys($this->getSearchableFields());

        $model = DynamicModel::validateData(\Yii::$app->request->get($this->filterVar, []), $this->rules());
        if ($model->hasErrors()) {
            throw new BadRequestHttpException($model->errors);
        }
    }

    /**
     * Подготовка условий сортировки
     * @return void
     *   ```
     * [
     *      'поле' => порядок сортировки
     * ]
     * ```
     */
    public function setSort(): void
    {
        $sortField = \Yii::$app->request->get('sort', '');

        // Выставляем фильтрацию по умолчанию
        if (
            $this->defaultSortField &&
            $this->defaultSortOrder &&
            !$sortField
        ) {
            $this->sort = [$this->defaultSortField => $this->defaultSortOrder];
            return;
        }

        $fields = $this->getSortableFields();

        if ($fields && $sortField) {
            $alias = $sortField;
            $direction = SORT_ASC;

            if ($alias[0] === '-') {
                $direction = SORT_DESC;
                $alias = mb_substr($alias, 1);
            }

            // Разворачиваем все поля для сортировки
            foreach ($fields as $key => $field) {
                // Будем готовить только те поля, которые вписаны в getSortableFields
                if ($key === $alias) {
                    $this->sort = [$field => $direction];
                }
            }
        }
    }

    /**
     * Подготовка данных для таблицы
     */
    public function setDataProvider($collection): ArrayDataProvider
    {
        return new ArrayDataProvider([
            'allModels' => $collection,
            'totalCount' => count($collection),
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'attributes' => $this->getSortableFields(),
            ],
        ]);
    }
}
