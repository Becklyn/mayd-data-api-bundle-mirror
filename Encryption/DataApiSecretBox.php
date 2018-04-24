<?php declare(strict_types=1);

namespace Mayd\DataApiBundle\Encryption;


/**
 * A secret box for data from the API
 */
class DataApiSecretBox
{
    /**
     * @var string
     */
    private $cipherText;


    /**
     * @var string
     */
    private $nonce;


    /**
     *
     * @param string $cipherText
     * @param string $nonce
     */
    public function __construct (string $cipherText, string $nonce)
    {
        $this->cipherText = $cipherText;
        $this->nonce = $nonce;
    }



    /**
     * Opens the secret box with the given secret
     *
     * @param string $secret
     * @return array|null
     */
    public function open (string $secret) : ?array
    {
        $data = sodium_crypto_secretbox_open(
            $this->cipherText,
            $this->nonce,
            $secret
        );

        return false !== $data
            ? \json_decode($data, true)
            : null;
    }


    /**
     * Creates a new secret box from any data
     *
     * @param array $data
     */
    public static function createFromData (string $secret, array $data) : self
    {
        $plainText = \json_encode($data);
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

        return new self(
            sodium_crypto_secretbox(
                $plainText,
                $nonce,
                $secret
            ),
            $nonce
        );
    }


    /**
     * Takes the request parameters and tries to generate a secret box from them
     *
     * @param array $apiData
     * @return DataApiSecretBox|null
     */
    public static function fromArray (array $apiData) : ?self
    {
        // "p" is for payload and "n" is for nonce
        if (!isset($apiData["p"], $apiData["n"])
            || !\is_string($apiData["p"])
            || !\is_string($apiData["n"])
        )
        {
            return null;
        }

        return new self(
            \base64_decode($apiData["p"]),
            \base64_decode($apiData["n"])
        );
    }


    /**
     * Serializes the secret box to an array
     */
    public function toArray ()
    {
        // "p" is for payload and "n" is for nonce
        return [
            "p" => \base64_encode($this->cipherText),
            "n" => \base64_encode($this->nonce),
        ];
    }
}
