<?php
// insert_notice.php (نسخة Debug مؤقتة)
header("Content-Type: application/json; charset=utf-8");
require_once __DIR__ . "/config.php";

// أظهر الأخطاء مؤقتًا
ini_set('display_errors', 1);
error_reporting(E_ALL);

$raw = file_get_contents("php://input");
if ($raw === false) {
  echo json_encode(["ok"=>false, "error"=>"Cannot read php://input"]);
  exit;
}
if ($raw === "" || $raw === null) {
  echo json_encode(["ok"=>false, "error"=>"Empty request body"]);
  exit;
}

$data = json_decode($raw, true);
if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
  echo json_encode(["ok"=>false, "error"=>"Invalid JSON", "detail"=>json_last_error_msg(), "raw"=>$raw]);
  exit;
}

$required = ["type","title","description"];
foreach ($required as $k) {
  if (empty($data[$k])) {
    echo json_encode(["ok"=>false, "error"=>"Missing field: $k", "got"=>$data]);
    exit;
  }
}

$user_id    = isset($data["user_id"]) ? trim($data["user_id"]) : "";
$name       = isset($data["name"]) ? trim($data["name"]) : "";
$contact    = isset($data["contact"]) ? trim($data["contact"]) : "";
$type       = trim($data["type"]);
$title      = trim($data["title"]);
$description= trim($data["description"]);
$location   = isset($data["location"]) ? trim($data["location"]) : "";
$trip_dates = isset($data["trip_dates"]) ? trim($data["trip_dates"]) : "";

try {
  $sql = "INSERT INTO notices
    (user_id, name, contact, type, title, description, location, trip_dates, created_at)
    VALUES
    (:user_id, :name, :contact, :type, :title, :description, :location, :trip_dates, NOW())";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ":user_id" => $user_id,
    ":name" => $name,
    ":contact" => $contact,
    ":type" => $type,
    ":title" => $title,
    ":description" => $description,
    ":location" => $location,
    ":trip_dates" => $trip_dates,
  ]);
  echo json_encode(["ok"=>true, "id"=>$pdo->lastInsertId()]);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(["ok"=>false, "error"=>"Insert failed", "detail"=>$e->getMessage()]);
}
