<?php
// CORS 헤더 설정
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin === 'https://strata-management-xi-five.vercel.app') {
    header("Access-Control-Allow-Origin: $origin");
}
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit(0);

setcookie("strata_session", "active", [
  'httponly' => true,
  'secure' => true,
  'samesite' => 'Strict',
  'path' => '/',
]);

setcookie("user_id", $user["id"], [
  'httponly' => true,
  'secure' => true,
  'samesite' => 'Strict',
  'path' => '/',
]);