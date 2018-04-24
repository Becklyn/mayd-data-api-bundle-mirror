<?php declare(strict_types=1);

namespace Mayd\DataApiBundle\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use Mayd\DataApiBundle\Encryption\DataApiEncryption;
use Mayd\DataApiBundle\Encryption\DataApiSecretBox;
use Mayd\DataApiBundle\Exception\ApiResponseException;


/**
 * Main handler to interact with the API
 */
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
     *
     * @param DataApiEncryption $encryption
     * @param string            $project
     * @param string            $endpointUrl
     */
    public function __construct (DataApiEncryption $encryption, string $project = "", string $endpointUrl = "")
    {
        $this->encryption = $encryption;
        $this->project = $project;

        $this->client = new Client([
            "base_uri" => rtrim($endpointUrl, "/") . "/",
            "headers" => [
                "Accept" => "application/json",
            ],
        ]);
    }


    /**
     * Sends a request to the API
     *
     * @param string $endpoint
     * @param array  $data
     * @return array|null
     */
    public function request (string $endpoint, array $data = []) : ?array
    {
        try
        {
            $secretBox = $this->encryption->encrypt($data);
            $response = $this->client->get(rtrim($endpoint, "/"), [
                "json" => \array_replace($secretBox->toArray(), [
                    "id" => $this->project,
                ]),
            ]);

            $data = \json_decode((string) $response->getBody(), true);

            if (null === $data || !is_array($data) || !isset($data["status"]))
            {
                throw new ApiResponseException("invalid_payload", "No parseable response given.");
            }

            if ("error" === $data["status"])
            {
                throw new ApiResponseException($data["error"] ?? "unknown_error", $data["message"] ?? "");
            }

            if ("ok" !== $data["status"])
            {
                throw new ApiResponseException("unknown_status", "Unknown status: {$data['status']}.");
            }

            $responseData = DataApiSecretBox::fromArray($data);

            if (null === $responseData)
            {
                return null;
            }

            return $this->encryption->decrypt($responseData);
        }
        catch (BadResponseException $e)
        {
            $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : "?";
            throw new ApiResponseException("request_failed", "The request has failed with status code {$statusCode}.", $e);
        }
        catch (GuzzleException $e)
        {
            throw new ApiResponseException("request_failed", "The request has failed.", $e);
        }
    }
}
