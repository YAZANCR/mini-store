<?php

require_once __DIR__ . '/functions.php';


// جلب المنتجات المتوفرة فقط
$products = db()
    ->query(
        'SELECT *
         FROM products
         WHERE stock > 0
         ORDER BY created_at DESC'
    )
    ->fetchAll();


// عنوان الصفحة
$pageTitle = 'المنتجات الفورية';


// استدعاء رأس الصفحة
require __DIR__ . '/header.php';

?>


<!-- القسم الرئيسي -->
<section class="hero">

    <h1>
        المنتجات الفورية
    </h1>

    <p>
        منتجات متوفرة حاليًا ويمكنك طلبها مباشرة من كولكشن.
    </p>

</section>


<!-- عنوان المنتجات -->
<h2 class="section-title">
    تسوق الآن
</h2>


<!-- قائمة المنتجات -->
<section class="stores">

    <?php foreach ($products as $p): ?>

        <article class="store-card">


            <!-- صورة المنتج -->
            <?php if ($p['image']): ?>

                <img
                    class="card-image"
                    src="assets/<?= e($p['image']) ?>"
                    alt="<?= e($p['name']) ?>"
                >

            <?php else: ?>

                <div class="product-icon">
                    🛍
                </div>

            <?php endif; ?>


            <!-- اسم المنتج -->
            <h3>
                <?= e($p['name']) ?>
            </h3>


            <!-- وصف المنتج -->
            <p>
                <?= e($p['description']) ?>
            </p>


            <!-- السعر -->
            <strong class="price">
                <?= e(
                    number_format(
                        (float) $p['price'],
                        2
                    )
                ) ?>
                ريال
            </strong>


            <!-- الكمية المتوفرة -->
            <small class="muted">
                المتوفر:
                <?= e($p['stock']) ?>
            </small>


            <?php if (user()): ?>

                <!-- نموذج طلب المنتج -->
                <form
                    action="submit_product_order.php"
                    method="post"
                    data-validate
                >

                    <!-- حماية CSRF -->
                    <input
                        type="hidden"
                        name="csrf"
                        value="<?= e(csrf_token()) ?>"
                    >


                    <!-- رقم المنتج -->
                    <input
                        type="hidden"
                        name="product_id"
                        value="<?= e($p['id']) ?>"
                    >


                    <!-- الكمية -->
                    <div class="form-row">

                        <label>
                            الكمية
                        </label>

                        <input
                            name="quantity"
                            type="number"
                            min="1"
                            max="<?= e($p['stock']) ?>"
                            value="1"
                            required
                        >

                    </div>


                    <!-- رقم الجوال -->
                    <div class="form-row">

                        <label>
                            رقم الجوال
                        </label>

                        <input
                            name="phone"
                            type="tel"
                            required
                        >

                    </div>


                    <!-- زر الطلب -->
                    <button
                        class="btn"
                        type="submit"
                    >
                        اطلب الآن
                    </button>

                </form>


            <?php else: ?>

                <!-- الزائر غير المسجل -->
                <a
                    class="btn"
                    href="login.php"
                >
                    سجل الدخول للطلب
                </a>

            <?php endif; ?>

        </article>

    <?php endforeach; ?>


    <!-- في حالة عدم وجود منتجات -->
    <?php if (!$products): ?>

        <p class="empty">
            لا توجد منتجات متوفرة حاليًا.
        </p>

    <?php endif; ?>

</section>


<?php

// استدعاء نهاية الصفحة
require __DIR__ . '/footer.php';

?>
