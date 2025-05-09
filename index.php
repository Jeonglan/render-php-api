<?php
header('Content-Type: application/json');

$db_url = getenv("DATABASE_URL");

if (!$db_url) {
  echo json_encode(["error" => "DATABASE_URL not set"]);
  exit;
}

$parsed_url = parse_url($db_url);
$host = $parsed_url['host'];
$db = ltrim($parsed_url['path'], '/');
$user = $parsed_url['user'];
$pass = $parsed_url['pass'];

try {
  $pdo = new PDO("pgsql:host=$host;dbname=$db", $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
  ]);
  echo json_encode(["status" => "connected to DB"]);
} catch (PDOException $e) {
  echo json_encode(["error" => $e->getMessage()]);
}
