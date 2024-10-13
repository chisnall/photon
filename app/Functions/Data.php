<?php

declare(strict_types=1);

namespace App\Functions;

use App\Core\Database\Connection;
use App\Models\CollectionModel;
use App\Models\SettingsModel;

class Data
{
    public static function records(string $sql): array
    {
        $statement = db()->prepare($sql);
        $statement->execute();
        $data = $statement->fetchAll(Connection::FETCH_UNIQUE|Connection::FETCH_ASSOC);

        return $data;
    }

    public static function variables(?int $collectionId): array
    {
        // Get global variables
        $globalVariables = SettingsModel::variables(user()->id());

        // Get collection variables
        if ($collectionId) {
            $collectionVariables = CollectionModel::variables($collectionId);
            $requestVariables = session()->get("variables/$collectionId") ?? [];
        } else {
            $collectionVariables = [];
            $requestVariables = [];
        }

        // Merge arrays - give priority to the request variables where keys clash, followed by collection variables
        $variables = array_merge($globalVariables, $collectionVariables, $requestVariables);

        return $variables;
    }
}
