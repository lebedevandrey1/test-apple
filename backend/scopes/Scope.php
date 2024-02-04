<?php

declare(strict_types=1);

namespace backend\scopes;

class Scope extends BaseScope
{
    public function select(array $columns): self
    {
        $this->getQuery()->select($columns);

        return $this;
    }

    public function andWhere(array $condition): self
    {
        $this->getQuery()->andWhere($condition);

        return $this;
    }
}
