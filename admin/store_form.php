<?php

require_once __DIR__ . '/../functions.php';

require_admin();



// تحديد المتجر في حالة التعديل


$id = (int) ($_GET['id'] ?? 0);

$store = [
    'name'        => '',
    'description' => '',
    'url'         => '',
    'image'       => null
];


// إذا كان هناك ID، نحاول جلب المتجر
if ($id > 0) {

    $q = db()->prepare(
        'SELECT * FROM stores WHERE id = ?'
    );

    $q->execute([$id]);

    $store = $q->fetch() ?: $store;
}



// معالجة النموذج

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    check_csrf();

    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $url = trim($_POST['url'] ?? '');


    
    // التحقق من البيانات
    
    if (mb_strlen($name) < 2) {
        $errors[] = 'اسم المتجر مطلوب.';
    }

    if ($description === '') {
        $errors[] = 'الوصف مطلوب.';
    }

    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        $errors[] = 'رابط المتجر غير صحيح.';
    }


    

    $image = $store['image'] ?? null;

    try {

        $image = upload_image(
            'image',
            $store['image'] ?? null
        );

    } catch (Throwable $e) {

        $errors[] = $e->getMessage();

    }


    
    if (!$errors) {

        if ($id > 0) {

            // تعديل متجر موجود
            $q = db()->prepare(
                'UPDATE stores
                 SET name = ?,
                     description = ?,
                     url = ?,
                     image = ?
                 WHERE id = ?'
            );

            $q->execute([
                $name,
                $description,
                $url,
                $image,
                $id
            ]);

            flash('تم تعديل المتجر.');

        } else {

            // إضافة متجر جديد
            $q = db()->prepare(
                'INSERT INTO stores
                 (name, description, url, image)
                 VALUES (?, ?, ?, ?)'
            );

            $q->execute([
                $name,
                $description,
                $url,
                $image
            ]);

            flash('تمت إضافة المتجر.');
        }

        redirect('index.php');
    }


    // الاحتفاظ بالبيانات عند وجود خطأ

    $store = [
        'name'        => $name,
        'description' => $description,
        'url'         => $url,
        'image'       => $image
    ];
}



$pageTitle = $id > 0
    ? 'تعديل متجر'
    : 'إضافة متجر';

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

            <label>اسم المتجر</label>

            <input
                name="name"
                required
                value="<?= e($store['name']) ?>"
            >

        </div>


        <div class="form-row">

            <label>الوصف</label>

            <textarea
                name="description"
                rows="4"
                required
            ><?= e($store['description']) ?></textarea>

        </div>


        <div class="form-row">

            <label>رابط المتجر</label>

            <input
                name="url"
                type="url"
                required
                value="<?= e($store['url']) ?>"
            >

        </div>


        <div class="form-row">

            <label>صورة الموقع</label>

            <input
                name="image"
                type="file"
                accept="image/jpeg,image/png,image/webp,image/gif"
            >

            <small class="muted">
                JPG أو PNG أو WEBP أو GIF، الحد الأقصى 3MB.
            </small>

        </div>


        <?php if (!empty($store['image'])): ?>

            <img
                class="card-image"
                src="../assets/<?= e($store['image']) ?>"
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