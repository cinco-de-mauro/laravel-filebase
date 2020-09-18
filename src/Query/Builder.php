<?php

namespace CincoDeMauro\LaravelFilebase\Query;

use Illuminate\Database\Query\Builder as BaseBuilder;

class Builder extends BaseBuilder
{
    protected function runSelect()
    {
        return $this->connection->selectUsingColumns(
            $this->columns, $this->wheres
        );
    }
}
