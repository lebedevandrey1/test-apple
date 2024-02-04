<?php

declare(strict_types=1);

namespace backend\controllers;

use Yii;
use yii\web\Controller;

abstract class BackendController extends Controller
{
    /**
     * @var array|null
     */
    protected ?array $filter = null;


    public function __construct($id, $module, $config = [])
    {
        $this->setFilter();

        parent::__construct($id, $module, $config);
    }

    /**
     * Инициализация фильтров при поиске
     */
    protected function setFilter(): void
    {
        $get = Yii::$app->request->get();
        $post = Yii::$app->request->post();

        $this->filter = $get['filter'] ?? $post['filter'] ?? null;
    }
}
