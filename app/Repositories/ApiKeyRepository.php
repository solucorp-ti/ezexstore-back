<?php

namespace App\Repositories;

use App\Models\ApiKey;
use App\Repositories\Interfaces\ApiKeyRepositoryInterface;
use Illuminate\Support\Str;

class ApiKeyRepository extends BaseRepository implements ApiKeyRepositoryInterface
{
    public function __construct(ApiKey $model)
    {
        parent::__construct($model);
    }

    public function findByKey(string $key)
    {
        return $this->model->where('key', $key)->first();
    }

    public function generateUniqueKey(): string
    {
        do {
            $key = Str::random(32);
        } while ($this->findByKey($key));

        return $key;
    }
}
