<?php

namespace App\Exceptions;

use Exception;

class StokHabisException extends Exception
{
    public function __construct(string $message = "Stok tidak mencukupi untuk penjualan.")
    {
        parent::__construct($message, 409);
    }
}