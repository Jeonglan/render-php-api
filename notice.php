<?php
header('Content-Type: application/json');

// Neon PostgreSQL connection string
$dsn = "pgsql:host=ep-autumn-glitter-a7pvixph-pooler.ap-southeast-2.aws.neon.tech;port=5432;dbname=neondb;sslmode=require";
$user = "neondb_owner";
$password = "npg_dI9wqKRNga8i";

try {
  // Connect to the database
  $pdo = new PDO($dsn, $user, $password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
  ]);

  // Example query: fetch all notices
  $stmt = $pdo->query("SELECT id, title, content, created_at FROM notices ORDER BY created_at DESC");

  $notices = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode([
    "success" => true,
    "data" => $notices
  ]);

} catch (PDOException $e) {
  echo json_encode([
    "success" => false,
    "error" => $e->getMessage()
  ]);
}
?>
