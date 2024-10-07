<?php

declare(strict_types=1);

namespace App\Database;

use App\Models\CollectionModel;
use App\Models\RequestModel;

class SeedExampleCollection
{
    private int $userId;
    private int $collectionId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function createCollection(): void
    {
        // Create collection record
        $collection = new CollectionModel();
        $collection->setProperty('userId', $this->userId);
        $collection->setProperty('collectionName', 'Example collection');
        $collection->setProperty('collectionVariables', [
            ['name' => 'BASE_URL', 'value' => 'https://reqres.in', 'enabled' => 'on'],
            ['name' => 'USER', 'value' => '2', 'enabled' => 'on'],
        ]);
        $collection->insertRecord();

        // Set ID of collection record
        $this->collectionId = $collection->getProperty('id');
    }

    public function createRequests(): void
    {
        // Define array for the request records - jsonplaceholder.typicode.com
        //$requestsData = [];
        //$requestsData[] = ['requestMethod' => 'get', 'requestUrl' => '{{BASE_URL}}/posts', 'requestName' => 'get posts', 'sortOrder' => 1];
        //$requestsData[] = ['requestMethod' => 'get', 'requestUrl' => '{{BASE_URL}}/posts/10', 'requestName' => 'get single post', 'sortOrder' => 2];
        //$requestsData[] = ['requestMethod' => 'post', 'requestUrl' => '{{BASE_URL}}/posts/', 'requestName' => 'create post', 'requestBody' => 'text', 'requestBodyTextValue' => "{\n    \"title\": \"foo\",\n    \"body\": \"bar\",\n    \"userId\": 1\n}", 'requestBodyTextType' => 'json', 'sortOrder' => 3];
        //$requestsData[] = ['requestMethod' => 'put', 'requestUrl' => '{{BASE_URL}}/posts/10', 'requestName' => 'update post (PUT)', 'requestBody' => 'text', 'requestBodyTextValue' => "{\n    \"title\": \"foo new\",\n    \"body\": \"bar new\",\n    \"userId\": 1\n}", 'requestBodyTextType' => 'json', 'sortOrder' => 4];
        //$requestsData[] = ['requestMethod' => 'patch', 'requestUrl' => '{{BASE_URL}}/posts/10', 'requestName' => 'update post (PATCH)', 'requestBody' => 'text', 'requestBodyTextValue' => "{\n    \"title\": \"foo new\"\n}", 'requestBodyTextType' => 'json', 'sortOrder' => 5];
        //$requestsData[] = ['requestMethod' => 'delete', 'requestUrl' => '{{BASE_URL}}/posts/10', 'requestName' => 'delete post', 'sortOrder' => 6];

        // Define array for the request records - reqres.in
        $requestsData = [];
        $requestsData[] = ['requestMethod' => 'get', 'requestUrl' => '{{BASE_URL}}/api/users', 'requestName' => 'get users', 'sortOrder' => 1];
        $requestsData[] = ['requestMethod' => 'get', 'requestUrl' => '{{BASE_URL}}/api/users/{{USER}}', 'requestName' => 'get single user', 'sortOrder' => 2];
        $requestsData[] = ['requestMethod' => 'post', 'requestUrl' => '{{BASE_URL}}/api/users/', 'requestName' => 'create user', 'requestBody' => 'text', 'requestBodyTextValue' => "{\n    \"email\": \"dave.smith@reqres.in\",\n    \"first_name\": \"Dave\",\n    \"last_name\": \"Smith\"\n}", 'requestBodyTextType' => 'json', 'sortOrder' => 3];
        $requestsData[] = ['requestMethod' => 'put', 'requestUrl' => '{{BASE_URL}}/api/users/{{USER}}', 'requestName' => 'update user (PUT)', 'requestBody' => 'text', 'requestBodyTextValue' => "{\n    \"email\": \"dave.smith@gmail.com\",\n    \"first_name\": \"Dave\",\n    \"last_name\": \"Smith\"\n}", 'requestBodyTextType' => 'json', 'sortOrder' => 4];
        $requestsData[] = ['requestMethod' => 'patch', 'requestUrl' => '{{BASE_URL}}/api/users/{{USER}}', 'requestName' => 'update user (PATCH)', 'requestBody' => 'text', 'requestBodyTextValue' => "{\n    \"email\": \"dave.smith@gmail.com\"\n}", 'requestBodyTextType' => 'json', 'sortOrder' => 5];
        $requestsData[] = ['requestMethod' => 'delete', 'requestUrl' => '{{BASE_URL}}/api/users/{{USER}}', 'requestName' => 'delete user', 'sortOrder' => 6];

        // Create records
        foreach ($requestsData as $requestData) {
            $request = new RequestModel();
            $request->setProperty('collectionId', $this->collectionId);
            $request->setProperty('requestMethod', $requestData['requestMethod']);
            $request->setProperty('requestUrl', $requestData['requestUrl']);
            $request->setProperty('requestName', $requestData['requestName']);
            if (array_key_exists('requestBody', $requestData)) $request->setProperty('requestBody', $requestData['requestBody']);
            if (array_key_exists('requestBodyTextValue', $requestData)) $request->setProperty('requestBodyTextValue', $requestData['requestBodyTextValue']);
            if (array_key_exists('requestBodyTextType', $requestData)) $request->setProperty('requestBodyTextType', $requestData['requestBodyTextType']);
            $request->setProperty('sortOrder', $requestData['sortOrder']);
            $request->insertRecord();
        }
    }
}
