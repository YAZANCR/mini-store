<?php

require_once __DIR__ . '/../functions.php';

require_admin();




$id = (int) ($_GET['id'] ?? 0);

$p = [
    'name'        => '',
    'description' => '',
    'price'       => '',
    'stock'       => '',
    'image'       => null
];


// إذا كان هناك ID، نحاول جلب المنتج
if ($id > 0) {

    $q = db()->prepare(
        'SELECT * FROM products WHERE id = ?'
    );

    $q->execute([$id]);

    $p = $q->fetch() ?: $p;
}




$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    check_csrf();

    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float) ($_POST['price'] ?? -1);
    $stock = (int) ($_POST['stock'] ?? -1);


    
    if (mb_strlen($name) < 2) {
        $errors[] = 'اسم المنتج مطلوب.';
    }

    if ($description === '') {
        $errors[] = 'وصف المنتج مطلوب.';
    }

    if ($price < 0) {
        $errors[] = 'السعر يجب أن يكون صفرًا أو أكثر.';
    }

    if ($stock < 0) {
        $errors[] = 'المخزون غير صحيح.';
    }


    
    $image = $p['image'] ?? null;

    try {

        $image = upload_image(
            'image',
            $p['image'] ?? null
        );

    } catch (Throwable $e) {

        $errors[] = $e->getMessage();

    }


    
    // حفظ البيانات
    
    if (!$errors) {

        if ($id > 0) {

            // تعديل منتج موجود
            $q = db()->prepare(
                'UPDATE products
                 SET name = ?,
                     description = ?,
                     price = ?,
                     stock = ?,
                     image = ?
                 WHERE id = ?'
            );

            $q->execute([
                $name,
                $description,
                $price,
                $stock,
                $image,
                $id
            ]);

            flash('تم تعديل المنتج.');

        } else {

            // إضافة منتج جديد
            $q = db()->prepare(
                'INSERT INTO products
                 (name, description, price, stock, image)
                 VALUES (?, ?, ?, ?, ?)'
            );

            $q->execute([
                $name,
                $description,
                $price,
                $stock,
                $image
            ]);

            flash('تمت إضافة المنتج.');
        }

        redirect('index.php');
    }


    // الاحتفاظ بالبيانات التي أدخلها المسؤول
    // في حالة وجود أخطاء

    $p = [
        'name'        => $name,
        'description' => $description,
        'price'       => $price,
        'stock'       => $stock,
        'image'       => $image
    ];
}



$pageTitle = $id > 0
    ? 'تعديل منتج'
    : 'إضافة منتج';

require __DIR__ . '/../header.php';

?>

<section class="form-card">

    <h1><?= e($pageTitle) ?></h1>


    <?php foreach ($errors as $error): ?>

        <div class="alert error">
            <?= e($error) ?>
        </div>

    <?php endforeach; ?>


    <form
        method="post"
        enctype="multipart/form-data"
        data-validate
    >

        <input
            type="hidden"
            name="csrf"
            value="<?= e(csrf_token()) ?>"
        >


        <div class="form-row">

            <label>اسم المنتج</label>

            <input
                name="name"
                required
                value="<?= e($p['name']) ?>"
            >

        </div>


        <div class="form-row">

            <label>الوصف</label>

            <textarea
                name="description"
                rows="4"
                required
            ><?= e($p['description']) ?></textarea>

        </div>


        <div class="form-row">

            <label>السعر بالريال</label>

            <input
                name="price"
                type="number"
                min="0"
                step="0.01"
                required
                value="<?= e($p['price']) ?>"
            >

        </div>


        <div class="form-row">

            <label>الكمية المتوفرة</label>

            <input
                name="stock"
                type="number"
                min="0"
                required
                value="<?= e($p['stock']) ?>"
            >

        </div>


        <div class="form-row">

            <label>صورة المنتج</label>

            <input
                name="image"
                type="file"
                accept="image/jpeg,image/png,image/webp,image/gif"
            >

            <small class="muted">
                JPG أو PNG أو WEBP أو GIF، الحد الأقصى 3MB.
            </small>

        </div>


        <?php if (!empty($p['image'])): ?>

            <img
                class="card-image"
                src="../assets/<?= e($p['image']) ?>"
                alt="الصورة الحالية"
            >

        <?php endif; ?>


        <button class="btn" type="submit">
            حفظ
        </button>

        <a class="btn gold" href="index.php">
            إلغاء
        </a>

    </form>

</section>


<?php require __DIR__ . '/../footer.php'; ?>