<?php
header("Access-Control-Allow-Origin: https://strata-management-xi-five.vercel.app"); // 🔁 배포 도메인
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

// ✅ 쿠키 확인
if (!isset($_COOKIE["strata_session"]) || !isset($_COOKIE["user_id"])) {
  echo json_encode(["success" => false, "message" => "Not authenticated"]);
  exit;
}

$userId = $_COOKIE["user_id"];

// ✅ DB 연결
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
?>
