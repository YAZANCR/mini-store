<?php

require_once __DIR__ . '/../functions.php';



require_admin();



if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}



check_csrf();



$id = (int) ($_POST['id'] ?? 0);



if ($id > 0) {

    $q = db()->prepare(
        'DELETE FROM products WHERE id = ?'
    );

    $q->execute([
        $id
    ]);


    flash(
        'تم حذف المنتج.'
    );
}



redirect('index.php');