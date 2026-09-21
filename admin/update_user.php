<?php

require_once __DIR__ . '/../functions.php';

require_admin();


// السماح بطلبات POST فقط

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

check_csrf();


// استقبال البيانات

$id = (int) ($_POST['id'] ?? 0);
$role = $_POST['role'] ?? '';



// التحقق من البيانات


if (
    $id <= 0 ||
    !in_array($role, ['user', 'admin'], true)
) {
    flash('بيانات المستخدم غير صالحة.', 'error');
    redirect('index.php');
}



// منع المسؤول من إزالة صلاحية المسؤول عن نفسه


if (
    $id === (int) user()['id'] &&
    $role !== 'admin'
) {
    flash(
        'لا يمكنك إزالة صلاحية المسؤول من حسابك الحالي.',
        'error'
    );

    redirect('index.php');
}


// تحديث الصلاحية

$q = db()->prepare(
    'UPDATE users SET role = ? WHERE id = ?'
);

$q->execute([
    $role,
    $id
]);



// رسالة النجاح

flash('تم تحديث صلاحية المستخدم.');

redirect('index.php');