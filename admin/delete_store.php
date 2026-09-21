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



if ($id > 0) {

    $q = db()->prepare(
        'DELETE FROM stores WHERE id = ?'
    );

    $q->execute([
        $id
    ]);


    flash(
        'تم حذف المتجر.'
    );
}



redirect('index.php');