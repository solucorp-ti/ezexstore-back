<?php

namespace App\Services\Interfaces;

interface InventoryLogServiceInterface
{
    public function getLogs(int $tenantId, array $filters = []);
}
