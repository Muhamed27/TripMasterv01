<?php
// يُرجع قائمة خطط المستخدم كمصفوفة JSON مباشرة ([] عند عدم وجود بيانات).
// فيه وضع تصحيح اختياري: ?debug=1 يطبع تفاصيل نصيّة تساعدك تعرف وين المشكلة.

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

header("Content-Type: application/json; charset=utf-8");
ini_set('display_errors', '0');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

require_once __DIR__ . "/db.php";
if (!isset($con) || !$con) { echo "[]"; exit; }

mysqli_set_charset($con, "utf8mb4");

// اقرأ uid بأي اسم شائع:
$uid = '';
foreach (['uid','userid','userId'] as $k) {
  if (!empty($_GET[$k])) { $uid = trim($_GET[$k]); break; }
  if (!empty($_POST[$k])) { $uid = trim($_POST[$k]); break; }
}
if ($uid === '') { echo "[]"; exit; }

$uidEsc = mysqli_real_escape_string($con, $uid);

$sql = "SELECT
          id, userid, titlePlan, startDate, endDate,
          smartDailyPlans, places, dailyHours,
          eventCalender, isActive, isShared, id_Shared_Trip, status
        FROM dashboard
        WHERE userid = '$uidEsc'
        ORDER BY id DESC
        LIMIT 200";

$res = mysqli_query($con, $sql);

if (!$res) {
  if (isset($_GET['debug'])) {
    header("Content-Type: text/plain; charset=utf-8");
    $db = mysqli_fetch_assoc(mysqli_query($con, "SELECT DATABASE() db"))['db'] ?? '(unknown)';
    echo "SQL ERROR: " . mysqli_error($con) . "\n";
    echo "DB: $db\n";
    echo "SQL: $sql\n";
  } else {
    echo "[]";
  }
  exit;
}

$out = [];
while ($row = mysqli_fetch_assoc($res)) {
  $out[] = [
    'id'              => (int)$row['id'],
    'userid'          => (string)$row['userid'],
    'titlePlan'       => (string)$row['titlePlan'],
    'startDate'       => (string)$row['startDate'],
    'endDate'         => (string)$row['endDate'],
    'smartDailyPlans' => (string)$row['smartDailyPlans'],
    'places'          => (string)$row['places'],
    'dailyHours'      => (string)$row['dailyHours'],
    // نخليها نص JSON كما هي (أو [] لو فاضية) — الداش القديم يتوقعها هيك
    'eventCalender'   => ($row['eventCalender'] !== null && $row['eventCalender'] !== '' ? (string)$row['eventCalender'] : '[]'),
    'isActive'        => (string)$row['isActive'],
    'isShared'        => (string)$row['isShared'],
    'id_Shared_Trip'  => ($row['id_Shared_Trip'] !== null ? (int)$row['id_Shared_Trip'] : null),
    'status'          => (string)$row['status'],
  ];
}

if (isset($_GET['debug'])) {
  header("Content-Type: text/plain; charset=utf-8");
  $db = mysqli_fetch_assoc(mysqli_query($con, "SELECT DATABASE() db"))['db'] ?? '(unknown)';
  echo "DB: $db\n";
  echo "UID: $uid\n";
  echo "Rows: " . count($out) . "\n";
  echo json_encode($out, JSON_UNESCAPED_UNICODE);
} else {
  echo json_encode($out, JSON_UNESCAPED_UNICODE);
}
