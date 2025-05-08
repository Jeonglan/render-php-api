<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$pdo = new PDO("pgsql:host=YOUR_HOST;port=5432;dbname=neondb;user=YOUR_USER;password=YOUR_PASS");

if ($_SERVER["REQUEST_METHOD"] === "GET") {
  $stmt = $pdo->query("SELECT id, name, description, status FROM facilities");
  $facilities = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode(["success" => true, "facilities" => $facilities]);
} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
  $data = json_decode(file_get_contents("php://input"), true);
  $name = $data["name"] ?? "";
  $description = $data["description"] ?? "";
  $status = $data["status"] ?? "available";

  if (!$name) {
    echo json_encode(["success" => false, "message" => "Facility name is required"]);
    exit;
  }

  $stmt = $pdo->prepare("INSERT INTO facilities (name, description, status) VALUES (?, ?, ?)");
  $stmt->execute([$name, $description, $status]);
  echo json_encode(["success" => true, "message" => "Facility added"]);
} else {
  http_response_code(405);
  echo json_encode(["success" => false, "message" => "Method not allowed"]);
}
