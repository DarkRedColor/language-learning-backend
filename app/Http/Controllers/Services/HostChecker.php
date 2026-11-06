<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Str;

class HostChecker extends Controller
{
    public function hostCheck($dbHost)
    {
        if ("production" == $dbHost){
            $host = 'production';
        }else{
            $host = 'uploads';
        }
        return $host;
    }
    public function pythonHostCheck($dbHost)
    {
        $host = 'https://gateway-microservice-production.langly.ai/api/progress/scene-content/api/progress/scene-content';

        if ($dbHost == "langly-php-postgresql-server.postgres.database.azure.com") {
            $host = 'https://gateway-microservice-stage.langly.ai/api/progress/scene-content';
        }
        if (Str::contains($dbHost, 'langly-postgresql-stage')) {
            $host = 'https://gateway-microservice-stage.langly.ai/api/progress/scene-content';
        }

        if ($dbHost == "127.0.0.1") {
            $host = 'https://gateway-microservice-stage.langly.ai/api/progress/scene-content';
        }
        if ($dbHost == "langly-postgresql-development-db-1666776251.postgres.database.azure.com") {
            $host = 'https://gateway-microservice-development.langly.ai/api/progress/scene-content';
        }
        if (Str::contains($dbHost, 'langly-postgresql-release-candidate')) {
            $host = 'https://gateway-microservice-release-candidate.langly.ai/api/progress/scene-content';
        }
        return $host;
    }

    public function jwtTokenGen()
    {
        $key = 'r)=&&zx2ii-h)n93m-!kb3ke)!gq=qzpq04v&0^98alg&0v@!7';
        $time = now();
        $expTime = $time->addMinutes(5);
        $payload = [
            "iat"=> $time,
            'exp' =>$expTime
        ];
        $jwt = JWT::encode($payload, $key);
        return "Bearer $jwt";
    }
}
