<?php

namespace App\Models\Concerns;

trait HasCompositeKey
{
    public function getIncrementing()
    {
        return false;
    }

    protected function setKeysForSelectQuery($query)
    {
        foreach ((array) $this->getKeyName() as $key) {
            $query->where($key, '=', $this->original[$key] ?? $this->getAttribute($key));
        }

        return $query;
    }

    protected function setKeysForSaveQuery($query)
    {
        return $this->setKeysForSelectQuery($query);
    }
}
