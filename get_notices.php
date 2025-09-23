<?php
require_once __DIR__ . "/config.php";

$page  = isset($_GET["page"]) ? max(1, intval($_GET["page"])) : 1;
$limit = 8;
$offset= ($page - 1) * $limit;

try {
  $total = $pdo->query("SELECT COUNT(*) AS c FROM notices")->fetch()["c"] ?? 0;

  $stmt = $pdo->prepare("
    SELECT id, user_id, name, contact, type, title, description, location, trip_dates, created_at
    FROM notices
    ORDER BY created_at DESC
    LIMIT :limit OFFSET :offset
  ");
  $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
  $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
  $stmt->execute();
  $rows = $stmt->fetchAll();

  echo json_encode([
    "ok" => true,
    "items" => $rows,
    "page" => $page,
    "total_pages" => max(1, ceil($total / $limit)),
  ]);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(["ok"=>false, "error"=>"Query failed", "detail"=>$e->getMessage()]);
}
