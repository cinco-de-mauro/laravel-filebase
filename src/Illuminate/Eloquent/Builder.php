<?php

namespace LaravelFilebase\Illuminate\Eloquent;

use Illuminate\Database\Query\Builder as BaseBuilder;

class Builder extends BaseBuilder
{
    protected function runSelect()
    {
        return $this->connection->selectFiles(
            $this->columns,
            $this->wheres
        );
    }

    public function insert(array $values)
    {
        if (empty($values)) {
            return true;
        }

        if (!is_array(reset($values))) {
            $values = [$values];
        } else {
            foreach ($values as $key => $value) {
                ksort($value);

                $values[$key] = $value;
            }
        }

        return $this->connection->insertFiles($values);
    }

    public function update(array $values)
    {
        return $this->connection->updateFiles($this->wheres, $values);
    }
}
