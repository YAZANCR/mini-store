USE collection_db;

-- حساب مدير تجريبي للمشروع المحلي:
-- البريد: admin@collection.local
-- كلمة المرور: Admin@12345
INSERT INTO users (name, email, password_hash, role)
VALUES ('مدير كولكشن', 'admin@collection.local', '$2y$10$tdFoGz7ZpTcNKXLkChP4g.HZCY5CXfPB7CiEdcD7K9riZ158i.z2a', 'admin')
ON DUPLICATE KEY UPDATE role = 'admin', password_hash = VALUES(password_hash);
