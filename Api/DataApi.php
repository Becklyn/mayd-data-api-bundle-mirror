<?php declare(strict_types=1);

namespace Mayd\DataApiBundle\Api;

use GuzzleHttp\Client;
use Mayd\DataApiBundle\Encryption\DataApiEncryption;


class DataApi
{
    /**
     * @var DataApiEncryption
     */
    private $encryption;


    /**
     * @var string
     */
    private $project;


    /**
     * @var string
     */
    private $endpointUrl;


    /**
     *
     * @param DataApiEncryption $encryption
     * @param string            $project
     * @param string            $endpointUrl
     */
    public function __construct (DataApiEncryption $encryption, string $project = "", string $endpointUrl = "")
    {
        $this->encryption = $encryption;
        $this->project = $project;
        $this->endpointUrl = rtrim($endpointUrl, "/");

        $this->client = new Client([
            "base_url" => $this->endpointUrl,
            "headers" => [
                "Accept" => "application/json",
            ],
        ]);
    }


    public function sendRequest (string $endpoint, array $data = []) : ?array
    {
        $secretBox = $this->encryption->encrypt($data);
        $payload = $secretBox->toArray();
        $payload["id"] = $this->project;

        try
        {
            $response = $this->client->get($endpoint, [
                "json" => $payload,
            ]);

            $data = \json_decode((string) $response->getBody(), true);
            dump($data);
        }
        catch (\Exception $e)
        {
            dump($e);
        }



    }
}
