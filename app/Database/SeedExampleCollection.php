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
        //$requestsData[] = ['requestMethod' => 'get', 'requestUrl' => '{{BASE_URL}}/posts', 'requestName' => '1) get posts'];
        //$requestsData[] = ['requestMethod' => 'get', 'requestUrl' => '{{BASE_URL}}/posts/10', 'requestName' => '2) get single post'];
        //$requestsData[] = ['requestMethod' => 'post', 'requestUrl' => '{{BASE_URL}}/posts/', 'requestName' => '3) create post', 'requestBody' => 'text', 'requestBodyTextValue' => "{\n    \"title\": \"foo\",\n    \"body\": \"bar\",\n    \"userId\": 1\n}", 'requestBodyTextType' => 'json'];
        //$requestsData[] = ['requestMethod' => 'put', 'requestUrl' => '{{BASE_URL}}/posts/10', 'requestName' => '4) update post (PUT)', 'requestBody' => 'text', 'requestBodyTextValue' => "{\n    \"title\": \"foo new\",\n    \"body\": \"bar new\",\n    \"userId\": 1\n}", 'requestBodyTextType' => 'json'];
        //$requestsData[] = ['requestMethod' => 'patch', 'requestUrl' => '{{BASE_URL}}/posts/10', 'requestName' => '5) update post (PATCH)', 'requestBody' => 'text', 'requestBodyTextValue' => "{\n    \"title\": \"foo new\"\n}", 'requestBodyTextType' => 'json'];
        //$requestsData[] = ['requestMethod' => 'delete', 'requestUrl' => '{{BASE_URL}}/posts/10', 'requestName' => '6) delete post'];

        // Define array for the request records - reqres.in
        $requestsData = [];
        $requestsData[] = ['requestMethod' => 'get', 'requestUrl' => '{{BASE_URL}}/api/users', 'requestName' => '1) get users'];
        $requestsData[] = ['requestMethod' => 'get', 'requestUrl' => '{{BASE_URL}}/api/users/{{USER}}', 'requestName' => '2) get single user'];
        $requestsData[] = ['requestMethod' => 'post', 'requestUrl' => '{{BASE_URL}}/api/users/', 'requestName' => '3) create user', 'requestBody' => 'text', 'requestBodyTextValue' => "{\n    \"email\": \"dave.smith@reqres.in\",\n    \"first_name\": \"Dave\",\n    \"last_name\": \"Smith\"\n}", 'requestBodyTextType' => 'json'];
        $requestsData[] = ['requestMethod' => 'put', 'requestUrl' => '{{BASE_URL}}/api/users/{{USER}}', 'requestName' => '4) update user (PUT)', 'requestBody' => 'text', 'requestBodyTextValue' => "{\n    \"email\": \"dave.smith@gmail.com\",\n    \"first_name\": \"Dave\",\n    \"last_name\": \"Smith\"\n}", 'requestBodyTextType' => 'json'];
        $requestsData[] = ['requestMethod' => 'patch', 'requestUrl' => '{{BASE_URL}}/api/users/{{USER}}', 'requestName' => '5) update user (PATCH)', 'requestBody' => 'text', 'requestBodyTextValue' => "{\n    \"email\": \"dave.smith@gmail.com\"\n}", 'requestBodyTextType' => 'json'];
        $requestsData[] = ['requestMethod' => 'delete', 'requestUrl' => '{{BASE_URL}}/api/users/{{USER}}', 'requestName' => '6) delete user'];

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
            $request->insertRecord();
        }
    }
}
