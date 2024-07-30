<?php
// MySQL table format
$sql = "CREATE TABLE IF NOT EXISTS migrations (
                id INTEGER PRIMARY KEY,
                migration TEXT NOT NULL,
                created_at TIMESTAMP NOT NULL
                );";

return $sql;
