<?php

declare(strict_types=1);

namespace amcintosh\FreshBooks\Resource;

use Http\Client\HttpClient;
use Spatie\DataTransferObject\DataTransferObject;
use amcintosh\FreshBooks\Builder\IncludesBuilder;
use amcintosh\FreshBooks\Exception\FreshBooksException;
use amcintosh\FreshBooks\Exception\FreshBooksNotImplementedException;
use amcintosh\FreshBooks\Model\DataModel;
use amcintosh\FreshBooks\Model\ListModel;
use amcintosh\FreshBooks\Model\VisState;

class BusinessResource extends BaseResource
{
    private HttpClient $httpClient;
    private string $businessPath;
    private string $singleModel;
    private string $listModel;
    private ?array $metaData;

    public function __construct(
        HttpClient $httpClient,
        string $businessPath,
        string $singleModel,
        string $listModel,
    ) {
        $this->httpClient = $httpClient;
        $this->singleModel = $singleModel;
        $this->listModel = $listModel;
        $this->businessPath = $businessPath;
    }

    /**
     * The the url to the business resource.
     *
     * @param  string $accountId
     * @param  int $resourceId
     * @return string
     */
    private function getUrl(string $accountId, int $resourceId = null): string
    {
        if (!is_null($resourceId)) {
            return "/auth/api/v1/businesses{$accountId}/{$this->businessPath}/{$resourceId}";
        }
        return "/auth/api/v1/businesses/{$accountId}/{$this->businessPath}";
    }

    /**
     * Make a request against the accounting resource and return an array of the json response.
     * Throws a FreshBooksException if the response is not a 200 or if the response cannot be parsed.
     *
     * @param  string $method
     * @param  string $url
     * @param  array $data
     * @return array
     */
    private function makeRequest(string $method, string $url, array $data = null): array
    {
        $this->metaData = null;

        if (!is_null($data)) {
            $data = json_encode($data);
        }
        $response = $this->httpClient->send($method, $url, [], $data);

        $statusCode = $response->getStatusCode();

        try {
            $contents = $response->getBody()->getContents();
            $responseData = json_decode($contents, true);
        } catch (JSONDecodeError $e) {
            throw new FreshBooksException('Failed to parse response', $statusCode, $e, $contents);
        }

        if (is_null($responseData) || !array_key_exists('response', $responseData)) {
            throw new FreshBooksException('Returned an unexpected response', $statusCode, null, $contents);
        }

        if (array_key_exists('meta', $responseData)) {
            $this->metaData = $responseData['meta'];
        }

        $responseData = $responseData['response'];

        if ($statusCode >= 400) {
            $this->createResponseError($statusCode, $responseData, $contents);
        }

        if (array_key_exists('result', $responseData)) {
            return $responseData['result'];
        }
        return $responseData;
    }

    /**
     * Parse the json response from the accounting endpoint and create a FreshBooksException from it.
     *
     * @param  int $statusCode HTTP status code
     * @param  array $responseData The json-parsed response
     * @param  string $rawRespone The raw response body
     * @return void
     */
    private function createResponseError(int $statusCode, array $responseData, string $rawRespone): void
    {
        if (!array_key_exists('errors', $responseData)) {
            throw new FreshBooksException('Unknown error', $statusCode, null, $rawRespone);
        }
        $errors = $responseData['errors'];
        if (array_key_exists(0, $errors)) {
            $message = $errors[0]['message'] ?? 'Unknown error2';
            $errorCode = $errors[0]['errno'] ?? null;
            throw new FreshBooksException($message, $statusCode, null, $rawRespone, $errorCode);
        }
        $message = $errors['message'] ?? 'Unknown error';
        $errorCode = $errors['errno'] ?? null;
        throw new FreshBooksException($message, $statusCode, null, $rawRespone, $errorCode);
    }

    /**
     * Get a single resource with the corresponding id.
     *
     * @param  string $accountId The alpha-numeric account id
     * @param  int $resourceId Id of the resource to return
     * @return DataTransferObject The result model
     */
    public function get(string $accountId, int $resourceId, ?IncludesBuilder $includes = null): DataTransferObject
    {
        $url = $this->getUrl($accountId, $resourceId) . $this->buildQueryString([$includes]);
        $result = $this->makeRequest(self::GET, $url);
        return new $this->singleModel($result);
    }

    /**
     * Get a list of resources.
     *
     * @param  string $accountId The alpha-numeric account id
     * @param  array $builders (Optional) List of builder objects for filters, pagination, etc.
     * @return DataTransferObject The list result model
     */
    public function list(string $accountId, ?array $builders = null): DataTransferObject
    {
        $url = $this->getUrl($accountId) . $this->buildQueryString($builders);
        $result = $this->makeRequest(self::GET, $url);
        if (!array_key_exists('meta', $result) && !is_null($this->metaData)) {
            $result = ['result' => $result, 'meta' => $this->metaData ];
            if (!array_key_exists('pages', $this->metaData)) {
                $result['meta']['pages'] = (int)($result['meta']['total']/$result['meta']['per_page'])+1;
            }
        }
        return new $this->listModel($result);
    }
}
