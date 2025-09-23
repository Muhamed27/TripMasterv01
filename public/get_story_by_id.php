<?php
// ===== CORS + JSON + إخفاء التحذيرات على الشاشة =====
$allowed = [
  'http://localhost:3000',
  'http://127.0.0.1:3000',
  'http://localhost:8012',
  'http://127.0.0.1:8012'
];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed, true)) {
  header("Access-Control-Allow-Origin: $origin");
} else {
  header("Access-Control-Allow-Origin: *"); // للـ Dev فقط
}
header("Vary: Origin");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// ردّ الـPreflight مباشرةً
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

header("Content-Type: application/json; charset=utf-8");

// لا نطبع تحذيرات HTML داخل الرد JSON
ini_set('display_errors', '0');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// ملاحظة مهمّة: أي echo/print/r var_dump قبل json_encode سيكسر JSON.
// للتصحيح استعمل error_log() بدل echo.
/**
 * API – פרטי סיפור בודד לפי id
 * קלט GET: id
 * פלט: { ok, item }
 */
require_once __DIR__.'/db.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) j_err('Missing id');

$sql = "SELECT id,user_id,trip_id,title,notes,country,images,rating,start_date,end_date,eventCalender,duration_days,created_at
        FROM stories WHERE id=$id LIMIT 1";
$res = mysqli_query($con, $sql);
if (!$res || mysqli_num_rows($res) === 0) j_err('Not found');

$item = mysqli_fetch_assoc($res);
$item['rating']        = isset($item['rating']) ? (int)$item['rating'] : null;
$item['images']        = json_try($item['images']);
$item['eventCalender'] = json_try($item['eventCalender']);

j_ok(['item'=>$item]);
