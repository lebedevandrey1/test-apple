<?php

declare(strict_types=1);

namespace backend\controllers;

use backend\forms\AppleForm;
use backend\grids\AppleGrid;
use backend\helpers\GridHelper;
use backend\repositories\AppleRepository;
use backend\services\AppleService;
use common\models\Apple;
use Yii;
use yii\web\Response;

class AppleController extends BackendController
{
    private GridHelper $gridHelper;
    private AppleService $service;
    private AppleRepository $repository;


    public function __construct(
        $id,
        $module,
        GridHelper $gridHelper,
        AppleService $service,
        AppleRepository $repository,
        $config = []
    ) {
        $this->service = $service;
        $this->repository = $repository;
        $this->gridHelper = $gridHelper;

        parent::__construct($id, $module, $config);
    }

    /**
     * Вывод всех доступных яблок
     */
    public function actionIndex(): string
    {
        $grid = new AppleGrid(
            AppleRepository::class
        );
        $grid->setSort();
        $grid->setFilter();
        $collection = $grid->search([
            'condition' => [
                '>', 'eat_part', 0
            ]
        ]);

        return $this->render('index', [
            'dataProvider' => $grid->setDataProvider($collection),
            'filterModel' => $grid,
            'gridHelper' => $this->gridHelper,
        ]);
    }

    /**
     * Создание рандомного кол-ва яблок
     */
    public function actionCreateRandom(): Response
    {
        $date = date('Y-m-d H:i:s');
        $quantity = rand(1, 5);

        $columns = ['title', 'color', 'status', 'eat_part', 'created_at', 'dropped_at'];

        $collection = [];
        for ($i = 1; $i <= $quantity; $i++) {
            $collection[$i]['title'] = 'Авто яблоко ' . $i;
            $collection[$i]['color'] = rand(1, 3);
            $collection[$i]['status'] = 0;
            $collection[$i]['eat_part'] = 100;
            $collection[$i]['created_at'] = $date;
            $collection[$i]['dropped_at'] = null;

            $i++;
        }

        $this->repository->batchInsert(Apple::tableName(), $columns, $collection);

        Yii::$app->getSession()->setFlash('success', 'Яблоки успешно созданы');
        return $this->redirect(['/apple']);
    }

    /**
     * Обновление яблока
     */
    public function actionUpdate(int $id): Response|string
    {
        $item = $this->service->findItem($id);
        $model = new AppleForm($item);
        $model->setIsNewRecord(false);

        if (Yii::$app->request->post()) {
            $model->setAttributes(Yii::$app->request->post());
            if ($model->validate()) {
                $newModel = $this->service->checkPostAndCreateParsedModel($model);
                $this->repository->save($newModel);

                Yii::$app->getSession()->setFlash('success', 'Яблоко ID=' . $newModel->id . ' успешно обновлено');
                return $this->redirect(['/apple']);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'gridHelper' => $this->gridHelper,
        ]);
    }
}
