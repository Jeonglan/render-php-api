<?php
// Allow CORS for local testing or Vercel
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Get JSON input from request
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['email']) || !isset($data['password'])) {
  echo json_encode(["success" => false, "message" => "Missing email or password"]);
  exit;
}

$email = $data['email'];
$password = $data['password'];

// Connect to Neon PostgreSQL
$host = 'your-neon-hostname';
$db   = 'neondb';
$user = 'your-username';
$pass = 'your-password';
$port = '5432';

$dsn = "pgsql:host=$host;port=$port;dbname=$db;user=$user;password=$pass";

try {
  $pdo = new PDO($dsn);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Check if user exists
  $stmt = $pdo->prepare("SELECT id, name, email, password_hash, role FROM users WHERE email = ?");
  $stmt->execute([$email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$user || !password_verify($password, $user['password_hash'])) {
    echo json_encode(["success" => false, "message" => "Invalid email or password"]);
    exit;
  }

  // Success
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
