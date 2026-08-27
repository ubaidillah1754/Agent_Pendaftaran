<?php

namespace App\Services;

/**
 * Exception khusus untuk error validasi antrean.
 * Digunakan agar controller bisa catch dengan tepat.
 */
class AntreanException extends \RuntimeException
{
    public function __construct(string $message, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
