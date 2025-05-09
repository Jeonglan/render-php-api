<?php
header('Content-Type: application/json');

// Handle the action parameter
$action = $_GET['action'] ?? null;

if ($action === "test") {
  echo json_encode([
    "message" => "PHP API is working!",
    "time" => date('c')
  ]);
  exit;
}

if ($action === "db") {
  // PostgreSQL DB connection info
  $dsn = "pgsql:host=ep-autumn-glitter-a7pvixph-pooler.ap-southeast-2.aws.neon.tech;port=5432;dbname=neondb;sslmode=require";
  $user = "neondb_owner";
  $password = "npg_dI9wqKRNga8i";

  try {
    // Connect to DB
    $pdo = new PDO($dsn, $user, $password, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Sample DB query
    $stmt = $pdo->query("SELECT id, title, content, created_at FROM notices ORDER BY created_at DESC");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
      "success" => true,
      "count" => count($data),
      "data" => $data
    ]);

  } catch (PDOException $e) {
    echo json_encode([
      "success" => false,
      "error" => $e->getMessage()
    ]);
  }
  exit;
}

// Unknown action
http_response_code(400);
echo json_encode(["error" => "Invalid or missing action. Try ?action=test or ?action=db"]);
exit;