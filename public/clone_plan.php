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
/*  clone_plan.php
    יוצר רשומת תכנית חדשה ב-dashboard מתוך סיפור קיים.
    נקלט FormData מהלקוח. עובד עם mysqli ו-db.php.
*/

header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once __DIR__ . "/db.php";
mysqli_set_charset($con, "utf8mb4");

// --- קריאת פרמטרים ---
$story_id       = isset($_POST["story_id"]) ? (int)$_POST["story_id"] : 0;
$title          = isset($_POST["title"]) ? trim($_POST["title"]) : "My Trip";
$user_id        = isset($_POST["user_id"]) ? trim($_POST["user_id"]) : "";
$new_start_date = isset($_POST["new_start_date"]) ? trim($_POST["new_start_date"]) : "";
$duration_days  = isset($_POST["duration_days"]) ? (int)$_POST["duration_days"] : 1;

if (!$story_id || $user_id === "" || $new_start_date === "") {
  echo json_encode(["ok"=>false, "error"=>"Missing required fields"], JSON_UNESCAPED_UNICODE);
  exit;
}

try {
  // לקרוא את ה-eventCalender מהסיפור, כדי לשמור אותו גם בתכנית החדשה
  $sqlS = "SELECT eventCalender FROM stories WHERE id=$story_id LIMIT 1";
  $resS = mysqli_query($con, $sqlS);
  if (!$resS) throw new Exception(mysqli_error($con));
  $rowS = mysqli_fetch_assoc($resS);
  $eventJson = $rowS && $rowS["eventCalender"] ? $rowS["eventCalender"] : "[]";

  // חישוב endDate לפי מספר ימים
  $start = DateTime::createFromFormat("Y-m-d", $new_start_date);
  if (!$start) throw new Exception("Invalid date format");
  $end = clone $start;
  $d = max(1, $duration_days);
  $end->modify("+" . ($d - 1) . " day");
  $startDate = $start->format("Y-m-d");
  $endDate   = $end->format("Y-m-d");

  // שדות JSON ריקים למבנה הקיים
  $emptyJson = "[]";
  $titleEsc  = mysqli_real_escape_string($con, $title);
  $uidEsc    = mysqli_real_escape_string($con, $user_id);

  $sqlI = "INSERT INTO dashboard
            (userid, smartDailyPlans, places, dailyHours, startDate, endDate, isActive, titlePlan, eventCalender, status)
           VALUES
            ('$uidEsc', '$emptyJson', '$emptyJson', '$emptyJson', '$startDate', '$endDate', 1, '$titleEsc', '$eventJson', 'active')";
  if (!mysqli_query($con, $sqlI)) throw new Exception(mysqli_error($con));

  $newId = mysqli_insert_id($con);
  echo json_encode(["ok"=>true, "new_plan_id"=>$newId], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(["ok"=>false, "error"=>$e->getMessage()], JSON_UNESCAPED_UNICODE);
}
