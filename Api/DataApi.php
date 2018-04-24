<?php declare(strict_types=1);

namespace Mayd\DataApiBundle\Api;

use GuzzleHttp\Client;
use Mayd\DataApiBundle\Encryption\DataApiEncryption;
use Mayd\DataApiBundle\Encryption\DataApiSecretBox;
use Mayd\DataApiBundle\Exception\ApiResponseException;


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
            "base_uri" => $this->endpointUrl,
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

            if (null === $data || !is_array($data) || !isset($data["status"]))
            {
                throw new ApiResponseException("invalid_response_payload", "No parseable response given.");
            }

            if ("error" === $data["status"])
            {
                throw new ApiResponseException($data["error"] ?? "unknown_error", $data["message"] ?? "");
            }

            if ("ok" !== $data["status"])
            {
                throw new ApiResponseException("unknown_response_status", "Unknown status: {$data['status']}.");
            }

            $responseData = DataApiSecretBox::fromArray($data);

            if (null === $responseData)
            {
                return null;
            }

            return $this->encryption->decrypt($responseData);
        }
        catch (\Exception $e)
        {
            throw new ApiResponseException("request_failed", "The request has failed.", $e);
        }
    }
}
