<?php
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin === 'https://strata-management-xi-five.vercel.app') {
  header("Access-Control-Allow-Origin: $origin");
}
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit(0);

if (!isset($_COOKIE['user_id'])) {
  echo json_encode(["success" => false, "message" => "Not logged in"]);
  exit;
}

$userId = $_COOKIE['user_id'];

// ✅ DB 연결 정보
$host = 'ep-empty-snow-a7zntah4-pooler.ap-southeast-2.aws.neon.tech';
$db   = 'neondb';
$user = 'neondb_owner';
$pass = 'npg_opRN75kDBSGu';
$port = '5432';

$dsn = "pgsql:host=$host;port=$port;dbname=$db;user=$user;password=$pass";

try {
  $pdo = new PDO($dsn);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $stmt = $pdo->prepare("SELECT id, name, email, role FROM users WHERE id = ?");
  $stmt->execute([$userId]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$user) {
    echo json_encode(["success" => false, "message" => "User not found"]);
    exit;
  }

  echo json_encode(["success" => true, "user" => $user]);
} catch (PDOException $e) {
  echo json_encode(["success" => false, "message" => "DB error", "error" => $e->getMessage()]);
}
