<?php

declare(strict_types=1);

namespace backend\repositories;

use backend\scopes\Scope;
use common\models\Apple;

class AppleRepository extends BaseRepository
{
    protected function getClass(): string
    {
        return Apple::class;
    }

    public function scope(): Scope
    {
        return new Scope($this->getClass());
    }

    public function getItemById(int $id)
    {
        $scope = $this->scope();
        $scope->byId($id);
        $scope->asArray();

        return $this->execute($scope->one());
    }
}
