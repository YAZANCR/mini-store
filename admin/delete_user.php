<?php

require_once __DIR__ . '/../functions.php';


// =====================================================
// التأكد من أن المستخدم مسؤول
// =====================================================

require_admin();


// =====================================================
// السماح بطلبات POST فقط
// =====================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}



check_csrf();



$id = (int) ($_POST['id'] ?? 0);


// منع المسؤول من حذف حسابه الحالي

if ($id > 0 && $id !== (int) user()['id']) {

    $q = db()->prepare(
        'DELETE FROM users WHERE id = ?'
    );

    $q->execute([
        $id
    ]);


    flash(
        'تم حذف المستخدم وطلباته.'
    );

} else {

    flash(
        'لا يمكن حذف حسابك الحالي.',
        'error'
    );
}



redirect('index.php');