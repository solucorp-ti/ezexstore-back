<?php

namespace App\Exceptions;

use Exception;

class InventoryException extends Exception
{
    public const INSUFFICIENT_STOCK = 'insufficient_stock';
    public const INVALID_OWNERSHIP = 'invalid_ownership';
    
    protected $errorCode;
    protected $details;

    public function __construct(string $message, string $errorCode = null, array $details = [])
    {
        parent::__construct($message);
        $this->errorCode = $errorCode;
        $this->details = $details;
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    public function getDetails(): array
    {
        return $this->details;
    }
}