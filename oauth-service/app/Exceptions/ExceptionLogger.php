<?php

namespace Petronetto\Exceptions;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Exceptions\ValidationException;
use Throwable;

class ExceptionLogger
{
    /**
     * @param Throwable $t
     */
    public static function handle(Throwable $t, LoggerInterface $logger, ServerRequestInterface $request)
    {
        switch ($t) {
            case $t instanceof NotFoundHttpException:
            case $t instanceof NotAllowedHttpException:
            case $t instanceof NestedValidationException:
            case $t instanceof ValidationException:
                // Not log this errors
                break;
            case $t instanceof UnauthorizedException:
                $message = sprintf(
                    "%s: %s\nUnauthorized\nUser Token: %s",
                    date('Y-m-d H:i:s'),
                    $request->getMethod(),
                    (string) $request->getUri(),
                    $request->getHeader('Authorization')[0] ?? 'null'
                );
                $logger->error($message);

                break;
            default:
                $message = sprintf(
                    "%s: %s\nException: %s\nTrace:\n%s",
                    $request->getMethod(),
                    (string) $request->getUri(),
                    $t->getMessage(),
                    $t->getTraceAsString()
                );
                $logger->error($message);

                break;
        }
    }
}
