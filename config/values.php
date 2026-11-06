<?php

return [
    'api_url' => env("API_URL"),
    'host' => env("SLOT"),
    'version' => env('APP_VERSION'),
    'sftp' => env('SFTP_HOST'),
    'user' => env('SFTP_USER'),
    'password' => env('SFTP_PASSWORD'),
    'sftp_path' => env('SFTP_PATH', '/mnt/storage/uploads/file'),
    'telegram_bot_token' => env('TELEGRAM_BOT_TOKEN'),
    'debugBar' => env('DEBUGBAR_ENABLED'),
    'oxford_api_key' => env('OXFORD_API_KEY'),
    'oxford_base_url' => env('OXFORD_BASE_URL'),
    'google_oauth_client_id' => env('GOOGLE_OAUTH_CLIENT_ID'),
    'google_oauth_secret_key' => env('GOOGLE_OAUTH_SECRET_KEY'),
    'force_ssl' => env('FORCE_SSL', true),
    'python_login' => env('PYTHON_LOGIN'),
    'python_password' => env('PYTHON_PASSWORD'),
    'app_url' => env('APP_URL'),
];