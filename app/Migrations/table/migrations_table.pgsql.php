<?php
// MySQL table format
$sql = "CREATE TABLE IF NOT EXISTS migrations (
                id SERIAL PRIMARY KEY,
                migration VARCHAR(255) NOT NULL,
                created_at TIMESTAMP NOT NULL
                );";

return $sql;
