<?php
// ✅ CORS 설정
header("Access-Control-Allow-Origin: *");
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

  // ✅ 사용자 조회
  $stmt = $pdo->prepare("SELECT id, name, email, password_hash, role FROM users WHERE email = ?");
  $stmt->execute([$email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$user || !password_verify($password, $user['password_hash'])) {
    echo json_encode(["success" => false, "message" => "Invalid credentials"]);
    exit;
  }

  // ✅ 성공 응답
  echo json_encode([
    "success" => true,
    "user" => [
      "id" => $user["id"],
      "name" => $user["name"],
      "email" => $user["email"],
      "role" => $user["role"]
    ]
  ]);

} catch (PDOException $e) {
  echo json_encode(["success" => false, "message" => "DB error", "error" => $e->getMessage()]);
}
?>

setcookie("strata_session", "active", [
  'httponly' => true,
  'secure' => true,
  'samesite' => 'Strict',
  'path' => '/',
]);