<?php declare(strict_types=1);

namespace Mayd\DataApiBundle\Exception;


use Throwable;


class DataApiException extends \RuntimeException
{
    /**
     * @inheritDoc
     */
    public function __construct (string $message = "", Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
