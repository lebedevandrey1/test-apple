<?php

declare(strict_types=1);

namespace backend\services;

use backend\forms\AppleForm;
use backend\repositories\AppleRepository;
use yii\web\NotFoundHttpException;

class AppleService extends Service
{
    /**
     * @var AppleRepository
     */
    protected $repository;


    protected function getRepositoryClass(): string
    {
        return AppleRepository::class;
    }

    /**
     * Поиск записи по ID
     *
     * @param int $id ID записи {{ apple }}
     */
    public function findItem(int $id)
    {
        $item = $this->repository->getItemById($id);

        if ($item) {
            return $item;
        }

        throw new NotFoundHttpException('Запись не найдена');
    }

    /**
     * Подмена данных в объект модели
     */
    public function checkPostAndCreateParsedModel(AppleForm $model): AppleForm
    {
        if ($model->bite) {
            $model->eat_part = $model->getOldAttribute('eat_part') - $model->bite;
        }

        if ($model->action == 1) {
            $model->status = 1;
            $model->dropped_at = date('Y-m-d H:i:s');
        }

        if ($model->action == 2) {
            $model->eat_part = 0;
        }

        if ($model->action == 3) {
            $model->dropped_at = date('Y-m-d H:i:s', strtotime("-5 hours"));
        }

        return $model;
    }
}
