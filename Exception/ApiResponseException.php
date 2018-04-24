<?php declare(strict_types=1);

namespace Mayd\DataApiBundle\Exception;


use Throwable;


class ApiResponseException extends DataApiException
{
    /**
     * @var string
     */
    private $errorKey;


    /**
     * @inheritDoc
     */
    public function __construct (string $errorKey, string $message, Throwable $previous = null)
    {
        parent::__construct($message, $previous);
        $this->errorKey = $errorKey;
    }


    /**
     * @return string
     */
    public function getErrorKey () : string
    {
        return $this->errorKey;
    }
}
