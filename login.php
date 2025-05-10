<?php
// ✅ CORS setting
header("Access-Control-Allow-Origin: http://localhost:3002");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit(0);

// JSON 요청 파싱
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['email']) || !isset($data['password'])) {
  echo json_encode(["success" => false, "message" => "Missing email or password"]);
  exit;
}

$email = $data['email'];
$password = $data['password'];

// PostgreSQL information (Neon)
$host = 'ep-autumn-glitter-a7pvixph-pooler.ap-southeast-2.aws.neon.tech';
$db   = 'neondb';
$user = 'neondb_owner';
$pass = 'npg_dI9wqKRNga8i';
$port = '5432';
$dsn = "pgsql:host=$host;port=$port;dbname=$db;user=$user;password=$pass";

try {
  $pdo = new PDO($dsn);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // user
  $stmt = $pdo->prepare("SELECT id, name, email, password_hash, role FROM users WHERE email = ?");
  $stmt->execute([$email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$user || !password_verify($password, $user['password_hash'])) {
    echo json_encode(["success" => false, "message" => "Invalid email or password"]);
    exit;
  }

  // success responce 
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
  echo json_encode(["success" => false, "message" => "Database error", "error" => $e->getMessage()]);
  exit;
}
