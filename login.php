<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin === 'https://strata-management-xi-five.vercel.app') {
  header("Access-Control-Allow-Origin: $origin");
}
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit(0);

// ✅ 요청 데이터 읽기
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['email']) || !isset($data['password'])) {
  echo json_encode(["success" => false, "message" => "Missing fields"]);
  exit;
}

$email = $data['email'];
$password = $data['password'];

// ✅ DB 연결 정보 (Neon PostgreSQL)
$host = 'ep-empty-snow-a7zntah4-pooler.ap-southeast-2.aws.neon.tech';
$db   = 'neondb';
$user = 'neondb_owner';
$pass = 'npg_opRN75kDBSGu';
$port = '5432';

$dsn = "pgsql:host=$host;port=$port;dbname=$db;user=$user;password=$pass";

try {
  $pdo = new PDO($dsn);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // ✅ 사용자 조회
  $stmt = $pdo->prepare("SELECT id, name, email, password_hash, role FROM users WHERE email = ?");
  $stmt->execute([$email]);
  $userRow = $stmt->fetch(PDO::FETCH_ASSOC);

  // ✅ 로그인 실패 처리
  if (!$userRow || !password_verify($password, $userRow['password_hash'])) {
    echo json_encode(["success" => false, "message" => "Invalid credentials"]);
    exit;
  }

  // ✅ 로그인 성공 시 쿠키 설정 (echo 전에 반드시!)
  setcookie("strata_session", "active", [
    'httponly' => true,
    'secure' => true,
    'samesite' => 'Strict',
    'path' => '/',
  ]);

  setcookie("user_id", $userRow["id"], [
    'httponly' => true,
    'secure' => true,
    'samesite' => 'Strict',
    'path' => '/',
  ]);

  // ✅ 성공 응답
  echo json_encode([
    "success" => true,
    "user" => [
      "id" => $userRow["id"],
      "name" => $userRow["name"],
      "email" => $userRow["email"],
      "role" => $userRow["role"]
    ]
  ]);

} catch (PDOException $e) {
  echo json_encode(["success" => false, "message" => "DB error", "error" => $e->getMessage()]);
}
?>
