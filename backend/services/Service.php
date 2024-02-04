<?php

declare(strict_types=1);

namespace backend\services;

use backend\repositories\BaseRepository;

abstract class Service
{
    /**
     * @var BaseRepository
     */
    protected $repository;


    abstract protected function getRepositoryClass(): string;


    public function __construct()
    {
        $repositoryClass = $this->getRepositoryClass();

        if ($repositoryClass) {
            $this->repository = \Yii::createObject($repositoryClass);
        }
    }
}
