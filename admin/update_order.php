<?php

require_once __DIR__ . '/../functions.php';

require_admin();



// السماح بطلبات POST فقط

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

check_csrf();



// الحالات المسموح بها


$allowed = [
    'new',
    'reviewing',
    'ordered',
    'shipped',
    'delivered',
    'cancelled'
];

$status = $_POST['status'] ?? '';
$id = (int) ($_POST['id'] ?? 0);



// التحقق من البيانات

if (
    !in_array($status, $allowed, true) ||
    $id <= 0
) {
    flash('بيانات غير صالحة.', 'error');
    redirect('index.php');
}



// تحديث حالة الطلب


$q = db()->prepare(
    'UPDATE orders SET status = ? WHERE id = ?'
);

$q->execute([
    $status,
    $id
]);



flash('تم تحديث حالة الطلب.');

redirect('index.php');