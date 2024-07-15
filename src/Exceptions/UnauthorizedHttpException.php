<?php

namespace Specialtactics\L5Api\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class UnauthorizedHttpException extends HttpException
{
    /**
     * @param string     $message  The internal exception message
     * @param \Exception $previous The previous exception
     * @param int        $code     The internal exception code
     * @param array      $headers
     */
    public function __construct(string $message = null, \Exception $previous = null, ?int $code = 0, array $headers = [])
    {
        parent::__construct(401, $message, $previous, $headers, $code);
    }
}
