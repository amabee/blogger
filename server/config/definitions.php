<?php

use App\Database;

return [
    Database::class => function () {
        return new Database(
            host: $_ENV['DB_HOST'],
            password: $_ENV['DB_PASSWORD'],
            username: $_ENV['DB_USERNAME'],
            dbname: $_ENV['DB_NAME']
        );
    }
]

    ?>