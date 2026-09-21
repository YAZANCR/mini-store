<?php

require_once __DIR__ . '/config.php';

$message = [];


try {

    // الاتصال بقاعدة البيانات
    $pdo = db();


    // =====================================================
    // إنشاء جدول المنتجات
    // =====================================================

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS products (

            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

            name VARCHAR(150) NOT NULL,

            description TEXT NOT NULL,

            price DECIMAL(10,2)
                NOT NULL DEFAULT 0,

            stock INT UNSIGNED
                NOT NULL DEFAULT 0,

            image VARCHAR(255)
                DEFAULT NULL,

            created_at TIMESTAMP
                DEFAULT CURRENT_TIMESTAMP,

            updated_at TIMESTAMP
                NULL DEFAULT NULL
                ON UPDATE CURRENT_TIMESTAMP

        ) ENGINE=InnoDB
    ");


    // =====================================================
    // التأكد من وجود أعمدة المنتجات في جدول الطلبات
    // =====================================================

    $columns = $pdo
        ->query('SHOW COLUMNS FROM orders')
        ->fetchAll(PDO::FETCH_COLUMN);


    // إضافة product_id إذا لم يكن موجودًا
    if (!in_array('product_id', $columns, true)) {

        $pdo->exec("
            ALTER TABLE orders
            ADD COLUMN product_id INT UNSIGNED NULL
            AFTER store_id
        ");
    }


    // إضافة quantity إذا لم يكن موجودًا
    if (!in_array('quantity', $columns, true)) {

        $pdo->exec("
            ALTER TABLE orders
            ADD COLUMN quantity INT UNSIGNED NOT NULL DEFAULT 1
            AFTER product_id
        ");
    }


    // =====================================================
    // إنشاء العلاقة بين orders و products
    // =====================================================

    try {

        $pdo->exec("
            ALTER TABLE orders
            ADD CONSTRAINT fk_orders_product
            FOREIGN KEY (product_id)
            REFERENCES products(id)
            ON DELETE SET NULL
        ");

    } catch (Throwable $ignored) {

        // إذا كانت العلاقة موجودة مسبقًا
        // يتم تجاهل الخطأ
    }


    // =====================================================
    // إضافة المنتجات الافتراضية
    // =====================================================

    $seed = $pdo->prepare(
        'SELECT COUNT(*) FROM products WHERE name = ?'
    );


    $insert = $pdo->prepare("
        INSERT INTO products
        (name, description, price, stock)
        VALUES (?, ?, ?, ?)
    ");


    $products = [

        [
            'سماعة لاسلكية',
            'سماعة بلوتوث بجودة صوت عالية وشحن سريع.',
            25,
            20
        ],

        [
            'حقيبة عملية',
            'حقيبة يومية أنيقة ومناسبة للاستخدام المتكرر.',
            35,
            12
        ],

        [
            'عطر فاخر',
            'عطر ثابت برائحة مميزة للاستخدام اليومي والمناسبات.',
            45,
            8
        ]

    ];


    foreach ($products as $product) {

        $seed->execute([
            $product[0]
        ]);


        // إذا لم يكن المنتج موجودًا
        // يتم إدخاله
        if (!$seed->fetchColumn()) {

            $insert->execute($product);
        }
    }


    // رسالة النجاح
    $message[] =
        'تم إنشاء/تحديث قاعدة البيانات بنجاح.';


} catch (Throwable $e) {

    // عرض رسالة الخطأ
    $message[] =
        'حدث خطأ: ' . $e->getMessage();
}

?>


<!doctype html>

<html lang="ar" dir="rtl">

<head>

    <meta charset="utf-8">

    <title>
        إعداد قاعدة البيانات
    </title>

</head>


<body
    style="
        font-family: Tahoma;
        padding: 40px;
        background: #f5f7f8;
    "
>

    <h1>
        إعداد قاعدة بيانات كولكشن
    </h1>


    <?php foreach ($message as $m): ?>

        <p>
            <?= htmlspecialchars(
                $m,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </p>

    <?php endforeach; ?>


    <p>
        إذا ظهرت رسالة النجاح،
        احذف ملف
        <b>setup_database.php</b>
        ثم افتح صفحة المنتجات.
    </p>


    <p>

        <a href="products.php">
            فتح المنتجات
        </a>

    </p>


</body>

</html>