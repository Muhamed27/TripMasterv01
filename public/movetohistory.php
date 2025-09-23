# path: public/movetohistory.php
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
  הזזה להיסטוריה (תאימות). 
  בפועל מספיק שה־UI מסתמך על endDate < היום; אם יש לך עמודת status — עדכן כאן.
  קלט POST: id
*/
require _DIR_.'/db.php';

$id = (int)($_POST['id'] ?? 0);
if ($id<=0) json_err('Missing id');

// אם יש עמודת status בטבלת plans — בטל את ההערה ושנה לשם העמודה האמיתי:
// $stmt = pdo()->prepare("UPDATE plans SET status='finished' WHERE id=?");
// $stmt->execute([$id]);

json_ok(['moved'=>true, 'id'=>$id]);
