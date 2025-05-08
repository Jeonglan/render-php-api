<?php
header('Content-Type: application/json');
echo json_encode([
  "message" => "PHP server is working!",
  "time" => date('c')
]);