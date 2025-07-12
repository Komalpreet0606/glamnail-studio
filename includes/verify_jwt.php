<?php
require 'jwt_config.php';
require '../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function verifyJWT($token)
{
    try {
        return JWT::decode($token, new Key(JWT_SECRET, 'HS256'));
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['message' => 'Unauthorized: ' . $e->getMessage()]);
        exit();
    }
}
