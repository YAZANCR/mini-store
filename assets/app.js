document.addEventListener('DOMContentLoaded', () => {

    // =====================================================
    // التحقق من النماذج
    // =====================================================

    document.querySelectorAll('form[data-validate]').forEach(form => {

        form.addEventListener('submit', event => {

            let valid = true;


            // =================================================
            // التحقق من الحقول المطلوبة
            // =================================================

            form.querySelectorAll('[required]').forEach(input => {

                input.classList.remove('invalid');

                if (!input.value.trim()) {
                    input.classList.add('invalid');
                    valid = false;
                }

            });


            // =================================================
            // التحقق من كلمة المرور
            // =================================================

            const password = form.querySelector('[name="password"]');
            const confirm = form.querySelector(
                '[name="password_confirmation"]'
            );

            if (
                password &&
                password.value.length < 8
            ) {
                password.classList.add('invalid');
                valid = false;
            }

            if (
                password &&
                confirm &&
                password.value !== confirm.value
            ) {
                confirm.classList.add('invalid');
                valid = false;
            }


            // =================================================
            // التحقق من رابط المنتج
            // =================================================

            const url = form.querySelector(
                '[name="product_url"]'
            );

            if (
                url &&
                !/^https?:\/\/\S+$/i.test(url.value.trim())
            ) {
                url.classList.add('invalid');
                valid = false;
            }


            // =================================================
            // منع الإرسال إذا كانت البيانات غير صحيحة
            // =================================================

            if (!valid) {

                event.preventDefault();

                alert(
                    'يرجى مراجعة الحقول المطلوبة والتأكد من صحة البيانات.'
                );
            }

        });

    });


    // =====================================================
    // رسائل تأكيد العمليات
    // =====================================================

    document.querySelectorAll('[data-confirm]').forEach(el => {

        el.addEventListener('click', event => {

            if (!confirm(el.dataset.confirm)) {
                event.preventDefault();
            }

        });

    });

});