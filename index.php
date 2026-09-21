
<?php

require_once __DIR__ . '/functions.php';

// جلب المتاجر من قاعدة البيانات
$stores = db()
    ->query('SELECT * FROM stores ORDER BY id')
    ->fetchAll();

// عنوان الصفحة
$pageTitle = 'الرئيسية';

// استدعاء رأس الصفحة
require __DIR__ . '/header.php';

?>

<!-- القسم الرئيسي -->
<section class="hero">

    <h1>
        مرحبًا بكم في <span>كولكشن</span>
    </h1>

    <p>
        اطلب منتجاتك من أشهر المتاجر الإلكترونية العالمية بسهولة.
    </p>

    <a
        class="btn gold"
        href="products.php"
    >
        شاهد المنتجات الفورية
    </a>

</section>


<!-- المتاجر -->
<h2 class="section-title">
    مواقعنا ومتاجرنا
</h2>

<section class="stores">

    <?php foreach ($stores as $store): ?>

        <article class="store-card">

            <?php if ($store['image']): ?>

                <img
                    class="card-image"
                    src="assets/<?= e($store['image']) ?>"
                    alt="<?= e($store['name']) ?>"
                >

            <?php endif; ?>

            <h3>
                <?= e($store['name']) ?>
            </h3>

            <p>
                <?= e($store['description']) ?>
            </p>

            <a
                href="<?= e($store['url']) ?>"
                target="_blank"
                rel="noopener"
            >
                زيارة المتجر ↗
            </a>

        </article>

    <?php endforeach; ?>

</section>


<!-- نموذج إرسال الطلب -->
<section
    id="order"
    class="form-card"
>

    <h2>
        أرسل رابط منتج من متجر عالمي
    </h2>


    <?php if (user()): ?>

        <!-- المستخدم مسجل الدخول -->

        <form
            action="submit_order.php"
            method="post"
            data-validate
        >

            <!-- حماية CSRF -->
            <input
                type="hidden"
                name="csrf"
                value="<?= e(csrf_token()) ?>"
            >


            <!-- اختيار المتجر -->
            <div class="form-row">

                <label>
                    المتجر
                </label>

                <select
                    name="store_id"
                    required
                >

                    <option value="">
                        اختر المتجر
                    </option>

                    <?php foreach ($stores as $store): ?>

                        <option
                            value="<?= e($store['id']) ?>"
                        >
                            <?= e($store['name']) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <!-- رابط المنتج -->
            <div class="form-row">

                <label>
                    رابط المنتج
                </label>

                <input
                    name="product_url"
                    type="url"
                    placeholder="https://..."
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


            <!-- الملاحظات -->
            <div class="form-row">

                <label>
                    ملاحظات اختيارية
                </label>

                <textarea
                    name="notes"
                    rows="3"
                ></textarea>

            </div>


            <!-- زر الإرسال -->
            <button
                class="btn"
                type="submit"
            >
                إرسال الطلب
            </button>

        </form>


    <?php else: ?>

        <!-- الزائر غير المسجل -->

        <p class="muted">
            سجّل الدخول لإرسال طلب من أحد المتاجر العالمية.
        </p>

        <a
            class="btn"
            href="login.php"
        >
            تسجيل الدخول
        </a>

    <?php endif; ?>

</section>


<?php

// استدعاء نهاية الصفحة
require __DIR__ . '/footer.php';

?>

