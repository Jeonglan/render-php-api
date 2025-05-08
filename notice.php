<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$pdo = new PDO("pgsql:host=YOUR_HOST;port=5432;dbname=neondb;user=YOUR_USER;password=YOUR_PASS");

if ($_SERVER["REQUEST_METHOD"] === "GET") {
  // Retrieve notice list
  $stmt = $pdo->query("SELECT id, title, content, date FROM notices ORDER BY date DESC");
  $notices = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode(["success" => true, "notices" => $notices]);
} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Create new notice
  $data = json_decode(file_get_contents("php://input"), true);
  $title = $data["title"] ?? "";
  $content = $data["content"] ?? "";

  if (!$title) {
    echo json_encode(["success" => false, "message" => "Title is required"]);
    exit;
  }

  $stmt = $pdo->prepare("INSERT INTO notices (title, content, date) VALUES (?, ?, CURRENT_DATE)");
  $stmt->execute([$title, $content]);
  echo json_encode(["success" => true, "message" => "Notice created"]);
} else {
  http_response_code(405);
  echo json_encode(["success" => false, "message" => "Method not allowed"]);
}
