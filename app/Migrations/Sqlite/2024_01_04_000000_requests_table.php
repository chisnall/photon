<?php

declare(strict_types=1);

return new class
{
    public function up(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS requests (
                id INTEGER PRIMARY KEY,
                collection_id INTEGER NOT NULL,
                request_method TEXT,
                request_url TEXT,
                request_name TEXT,
                request_params_inputs TEXT,
                request_headers_inputs TEXT,
                request_auth TEXT,
                request_auth_basic_username TEXT,
                request_auth_basic_password TEXT,
                request_auth_token_value TEXT,
                request_auth_header_name TEXT,
                request_auth_header_value TEXT,
                request_body TEXT,
                request_body_text_value TEXT,
                request_body_text_type TEXT,
                request_body_form_inputs TEXT,
                request_body_file TEXT,
                request_variables_inputs TEXT,
                created_at TIMESTAMP NOT NULL,
                updated_at TIMESTAMP NOT NULL,

                CONSTRAINT `requests_collection_id_fkey`
                    FOREIGN KEY (collection_id) REFERENCES collections (id)
                    ON DELETE CASCADE
                );";

        db()->exec($sql);
    }

    public function down(): void
    {
        $sql = "DROP TABLE requests;";

        db()->exec($sql);
    }
};
