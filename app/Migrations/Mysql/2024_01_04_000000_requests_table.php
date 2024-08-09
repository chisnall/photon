<?php

declare(strict_types=1);

use App\Core\Application;

return new class
{
    public function up(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS requests (
                id SERIAL PRIMARY KEY,
                collection_id BIGINT UNSIGNED NOT NULL,
                request_method VARCHAR(255),
                request_url TEXT,
                request_name VARCHAR(255),
                request_params_inputs TEXT,
                request_headers_inputs TEXT,
                request_auth VARCHAR(255),
                request_auth_basic_username VARCHAR(255),
                request_auth_basic_password VARCHAR(255),
                request_auth_token_value VARCHAR(255),
                request_auth_header_name VARCHAR(255),
                request_auth_header_value TEXT,
                request_body VARCHAR(255),
                request_body_text_value MEDIUMTEXT,
                request_body_text_type VARCHAR(255),
                request_body_form_inputs TEXT,
                request_body_file VARCHAR(255),
                request_variables_inputs MEDIUMTEXT,
                created_at TIMESTAMP NOT NULL,
                updated_at TIMESTAMP NOT NULL,

                CONSTRAINT `requests_collection_id_fkey`
                    FOREIGN KEY (collection_id) REFERENCES collections (id)
                    ON DELETE CASCADE
                );";

        Application::app()->db()->exec($sql);
    }

    public function down(): void
    {
        $sql = "DROP TABLE requests;";

        Application::app()->db()->exec($sql);
    }
};
