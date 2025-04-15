
<?php
header('Content-Type: application/json');

$dataFile = 'students.json';
$students = json_decode(file_get_contents($dataFile), true);

$input = json_decode(file_get_contents("php://input"), true);

$email = $input['email'];
$code = $input['code'];
$password = $input['password'];
$fingerprint = $input['fingerprint'];
$ip = $_SERVER['REMOTE_ADDR'];

if (!isset($students[$email])) {
    echo json_encode(["status" => "error", "message" => "هذا الجيميل غير مسجل"]);
    exit;
}

$student = $students[$email];

if ($student['code'] !== $code || $student['password'] !== $password) {
    echo json_encode(["status" => "error", "message" => "الكود أو كلمة السر غير صحيحة"]);
    exit;
}

if (empty($student['device']) && empty($student['ip'])) {
    $students[$email]['device'] = $fingerprint;
    $students[$email]['ip'] = $ip;
    file_put_contents($dataFile, json_encode($students, JSON_PRETTY_PRINT));
    echo json_encode(["status" => "success", "message" => "تم تسجيل الدخول لأول مرة"]);
    exit;
}

if ($student['device'] === $fingerprint && $student['ip'] === $ip) {
    echo json_encode(["status" => "success", "message" => "تم تسجيل الدخول"]);
} else {
    echo json_encode(["status" => "error", "message" => "هذا الحساب مسجل على جهاز أو شبكة مختلفة"]);
}
?>
