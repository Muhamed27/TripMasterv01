<?php
// نسخة مبسطة جدًا: تطبع نص واضح عند الخطأ، و JSON عند النجاح.
// لو فتحت بالرابط مع ?debug=1 هتظهر رسائل الخطأ كنص.

$debug = isset($_GET['debug']) && $_GET['debug'] == '1';
if ($debug) {
  ini_set('display_errors', '1');
  error_reporting(E_ALL);
  header('Content-Type: text/plain; charset=utf-8');
} else {
  header('Content-Type: application/json; charset=utf-8');
}

// جرّب تضمين db.php
$path = __DIR__ . '/db.php';
if (!file_exists($path)) {
  echo $debug ? "MISSING FILE: public/db.php\n" : "[]";
  exit;
}
require $path;

// تأكد أن الاتصال موجود
if (!isset($con) || !$con) {
  echo $debug ? "NO \$con from db.php (mysqli connection not set)\n" : "[]";
  exit;
}

// ترميز
mysqli_set_charset($con, 'utf8mb4');

// إقرأ uid بأي مفتاح شائع
$uid = '';
foreach (['uid','userid','userId'] as $k) {
  if (!empty($_GET[$k])) { $uid = trim($_GET[$k]); break; }
  if (!empty($_POST[$k])) { $uid = trim($_POST[$k]); break; }
}
if ($uid === '') {
  echo $debug ? "MISSING UID\n" : "[]";
  exit;
}

$uidEsc = mysqli_real_escape_string($con, $uid);

// الاستعلام
$sql = "SELECT id, titlePlan, startDate, endDate, eventCalender, status
        FROM dashboard
        WHERE userid = '$uidEsc'
        ORDER BY id DESC
        LIMIT 200";

$res = mysqli_query($con, $sql);
if (!$res) {
  echo $debug ? ("SQL ERROR: " . mysqli_error($con) . "\nSQL: $sql\n") : "[]";
  exit;
}

// بناء الخرج
$out = [];
while ($r = mysqli_fetch_assoc($res)) {
  $r['id'] = (int)$r['id'];
  // نخلي eventCalender نص JSON كما هو أو "[]"
  $r['eventCalender'] = ($r['eventCalender'] !== null && $r['eventCalender'] !== '') ? $r['eventCalender'] : '[]';
  $out[] = $r;
}

echo $debug
  ? json_encode($out, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
  : json_encode($out, JSON_UNESCAPED_UNICODE);
