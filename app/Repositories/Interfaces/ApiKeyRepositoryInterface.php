<?php

namespace App\Repositories\Interfaces;

interface ApiKeyRepositoryInterface extends BaseRepositoryInterface
{
    public function findByKey(string $key);

    public function generateUniqueKey(): string;
}
