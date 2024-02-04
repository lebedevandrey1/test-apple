<?php

declare(strict_types=1);

namespace backend\forms;

use backend\repositories\AppleRepository;
use backend\services\AppleService;
use common\models\Apple;
use Yii;

class AppleForm extends Apple
{
    private AppleService $service;
    private AppleRepository $repository;

    public $bite;
    public $action;


    public function __construct($config = [])
    {
        $this->service = Yii::createObject(AppleService::class);
        $this->repository = Yii::createObject(AppleRepository::class);

        parent::__construct($config);
    }


    public function rules(): array
    {

        return [

            ['title', 'required'],
            ['title', 'trim'],
            ['title', 'string', 'max' => 100],

            ['bite', 'trim'],
            ['bite', 'integer', 'min' => 1, 'max' => $this->eat_part, 'message' => 'Неверный диапазон'],
            ['bite', 'string', 'length' => [1, 3]],
            ['bite', 'canBite'],

            ['action', 'trim'],
            ['action', 'integer', 'min' => 1, 'max' => 3, 'message' => 'Неверный диапазон'],
            ['action', 'checkAction'],

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
            'dropped_at' => 'Дата падения',

            'bite' => 'Откусить (в %)',
            'action' => 'Действие',
        ];
    }

    public function formName()
    {
        return '';
    }

    public function canBite(string $attribute): bool
    {

        if (!$this->dropped_at) {
            $this->addError(
                $attribute,
                'Нельзя откусить яблоко, висящее на дереве'
            );

            return false;
        }

        if ($this->dropped_at < date('Y-m-d H:i:s', strtotime("-5 hours"))) {
            $this->addError(
                $attribute,
                'Испорченное яблоко лучше не кусать'
            );

            return false;
        }

        return true;
    }

    public function checkAction(string $attribute): bool
    {
        if ($this->$attribute == 1 && $this->dropped_at) {
            $this->addError(
                $attribute,
                'Яблоко уже упало, можно подбирать'
            );

            return false;
        }

        if ($this->$attribute == 2 && !$this->dropped_at) {
            $this->addError(
                $attribute,
                'Прежде чем съесть яблоко, сорвите его с дерева'
            );

            return false;
        } elseif (
            $this->$attribute == 2
            && $this->dropped_at
            && $this->dropped_at <= date('Y-m-d H:i:s', strtotime("-5 hours"))
        ) {
            $this->addError(
                $attribute,
                'Испорченное яблоко лучше не есть'
            );

            return false;
        }

        if ($this->$attribute == 3 && !$this->dropped_at) {
            $this->addError(
                $attribute,
                'Висящее на дереве яблоко испортиться не может'
            );

            return false;
        }

        return true;
    }
}
