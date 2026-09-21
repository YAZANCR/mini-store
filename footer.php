</main>


<footer>

    <div>

        <strong>كولكشن</strong>

        <span>
            منصة تساعدك على طلب منتجاتك من المتاجر العالمية.
        </span>


        <?php if (user()): ?>

            <small class="footer-status">
                الجلسة نشطة باسم:
                <?= e(user()['name']) ?>
            </small>

        <?php else: ?>

            <small class="footer-status">
                زائر — سجّل الدخول لحفظ طلباتك
            </small>

        <?php endif; ?>

    </div>


    <small>
        © <?= date('Y') ?> كولكشن — جميع الحقوق محفوظة
    </small>

</footer>


<script src="<?= $assetPrefix ?? '' ?>assets/app.js"></script>

</body>
</html>