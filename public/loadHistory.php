# path: public/loadHistory.php
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
/* 
  מחזיר נסיעות שהסתיימו עבור משתמש (לשונית History)
  קלט (POST): uid
  יציאה: מערך פריטים עם: id, titlePlan, eventCalender, images, startDate, endDate
  הערה: משתמש בטבלת plans (התאם לשם הטבלה אם אצלך אחרת)
*/
require __DIR__.'/db.php';

$uid = trim($_POST['uid'] ?? '');
if ($uid === '') json_ok([]); // ריק עדיף על שגיאה ב־UI

try {
  $sql = "SELECT id, titlePlan, eventCalender, images, startDate, endDate
          FROM plans
          WHERE user_id = ? AND (endDate IS NOT NULL AND endDate < CURDATE())
          ORDER BY endDate DESC";
  $stmt = pdo()->prepare($sql);
  $stmt->execute([$uid]);
  $rows = $stmt->fetchAll();
  foreach ($rows as &$r) {
    $r['eventCalender'] = safe_json_array($r['eventCalender']);
    $r['images'] = safe_json_array($r['images']);
  }
  echo json_encode($rows, JSON_UNESCAPED_UNICODE);
  exit;
} catch (Throwable $e) {
  // אם הטבלה/עמודות שונות — החזר ריק במקום להפיל את ה־UI
  echo json_encode([], JSON_UNESCAPED_UNICODE);
  exit;
}
