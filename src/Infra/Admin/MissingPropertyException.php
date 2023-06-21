<?php

namespace App\Infra\Admin;

class MissingPropertyException extends \Exception
{
    public function __construct(string $class, string $property, string $message = 'Missing property %s::%s', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf($message, $class, $property), $code, $previous);
    }
}
