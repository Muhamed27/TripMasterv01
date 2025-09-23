<?php
header('Content-Type: application/json; charset=utf-8');
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin === 'http://localhost:3000' || $origin === 'http://127.0.0.1:3000') {
  header("Access-Control-Allow-Origin: $origin");
  header("Access-Control-Allow-Credentials: true");
  header("Vary: Origin");
}
require_once __DIR__ . '/../db.php'; // عدّل المسار لو لزم
if (!isset($con) || !$con) { echo json_encode(['ok'=>false,'error'=>'DB']); exit; }

$rows = [];
$res = mysqli_query($con, "SELECT DISTINCT country FROM stories WHERE country IS NOT NULL AND country <> '' ORDER BY country");
if ($res) {
  while ($r = mysqli_fetch_assoc($res)) $rows[] = $r['country'];
}
echo json_encode(['ok'=>true,'countries'=>$rows], JSON_UNESCAPED_UNICODE);
