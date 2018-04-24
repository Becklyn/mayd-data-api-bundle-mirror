<?php declare(strict_types=1);

namespace Mayd\DataApiBundle\Encryption;


class DataApiEncryption
{
    /**
     * @var string
     */
    private $secret;


    /**
     *
     * @param string $secret
     */
    public function __construct (string $secret = "")
    {
        $this->secret = \base64_decode($secret);
    }


    /**
     * Encrypts the given data
     *
     * @param array $data
     * @return DataApiSecretBox
     */
    public function encrypt (array $data) : DataApiSecretBox
    {
        return DataApiSecretBox::createFromData($this->secret, $data);
    }


    /**
     * Decrypt the secret box
     *
     * @param DataApiSecretBox $secretBox
     * @return array|null
     */
    public function decrypt (DataApiSecretBox $secretBox) : ?array
    {
        return $secretBox->open($this->secret);
    }
}
