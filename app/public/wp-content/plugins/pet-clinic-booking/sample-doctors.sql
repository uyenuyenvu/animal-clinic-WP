-- Dữ liệu mẫu cho bảng wp_pcb_doctors
-- Chạy file này sau khi đã tạo bảng
-- Xóa dữ liệu cũ (nếu có)
TRUNCATE TABLE wp_pcb_doctors;
-- Thêm dữ liệu mẫu
INSERT INTO wp_pcb_doctors (
        name,
        specialization,
        phone,
        email,
        status,
        created_at,
        updated_at
    )
VALUES -- Bác sĩ 1: Chuyên khoa da liễu
    (
        '佐藤医師',
        '皮膚科・アレルギー科',
        '03-1234-5678',
        'sato@clinic.example.com',
        'active',
        '2024-01-15 09:00:00',
        '2024-01-15 09:00:00'
    ),
    -- Bác sĩ 2: Chuyên khoa nội khoa
    (
        '高橋医師',
        '内科・消化器科',
        '03-2345-6789',
        'takahashi@clinic.example.com',
        'active',
        '2024-01-20 09:00:00',
        '2024-01-20 09:00:00'
    ),
    -- Bác sĩ 3: Chuyên khoa phẫu thuật
    (
        '伊藤医師',
        '外科・整形外科',
        '03-3456-7890',
        'ito@clinic.example.com',
        'active',
        '2024-02-01 09:00:00',
        '2024-02-01 09:00:00'
    ),
    -- Bác sĩ 4: Chuyên khoa nha khoa
    (
        '渡辺医師',
        '歯科・口腔外科',
        '03-4567-8901',
        'watanabe@clinic.example.com',
        'active',
        '2024-02-10 09:00:00',
        '2024-02-10 09:00:00'
    ),
    -- Bác sĩ 5: Chuyên khoa tim mạch
    (
        '小林医師',
        '循環器科・心臓病科',
        '03-5678-9012',
        'kobayashi@clinic.example.com',
        'active',
        '2024-02-15 09:00:00',
        '2024-02-15 09:00:00'
    ),
    -- Bác sĩ 6: Chuyên khoa ung thư
    (
        '斎藤医師',
        '腫瘍科・がん治療科',
        '03-6789-0123',
        'saito@clinic.example.com',
        'active',
        '2024-03-01 09:00:00',
        '2024-03-01 09:00:00'
    ),
    -- Bác sĩ 7: Chuyên khoa thần kinh
    (
        '井上医師',
        '神経科・脳外科',
        '03-7890-1234',
        'inoue@clinic.example.com',
        'active',
        '2024-03-10 09:00:00',
        '2024-03-10 09:00:00'
    ),
    -- Bác sĩ 8: Chuyên khoa sản khoa
    (
        '田中医師',
        '産科・生殖医療科',
        '03-8901-2345',
        'tanaka@clinic.example.com',
        'active',
        '2024-03-20 09:00:00',
        '2024-03-20 09:00:00'
    ),
    -- Bác sĩ 9: Chuyên khoa dị ứng
    (
        '吉田医師',
        'アレルギー科・免疫科',
        '03-9012-3456',
        'yoshida@clinic.example.com',
        'active',
        '2024-04-01 09:00:00',
        '2024-04-01 09:00:00'
    ),
    -- Bác sĩ 10: Chuyên khoa tiết niệu
    (
        '中村医師',
        '泌尿器科・腎臓科',
        '03-0123-4567',
        'nakamura@clinic.example.com',
        'active',
        '2024-04-10 09:00:00',
        '2024-04-10 09:00:00'
    ),
    -- Bác sĩ 11: Chuyên khoa mắt
    (
        '山田医師',
        '眼科・視覚医療科',
        '03-1234-5679',
        'yamada@clinic.example.com',
        'active',
        '2024-04-20 09:00:00',
        '2024-04-20 09:00:00'
    ),
    -- Bác sĩ 12: Chuyên khoa tai mũi họng
    (
        '佐々木医師',
        '耳鼻咽喉科・聴覚医療科',
        '03-2345-6780',
        'sasaki@clinic.example.com',
        'active',
        '2024-05-01 09:00:00',
        '2024-05-01 09:00:00'
    ),
    -- Bác sĩ 13: Chuyên khoa chẩn đoán hình ảnh
    (
        '松本医師',
        '画像診断科・放射線科',
        '03-3456-7891',
        'matsumoto@clinic.example.com',
        'active',
        '2024-05-10 09:00:00',
        '2024-05-10 09:00:00'
    ),
    -- Bác sĩ 14: Chuyên khoa cấp cứu
    (
        '加藤医師',
        '救急科・集中治療科',
        '03-4567-8902',
        'kato@clinic.example.com',
        'active',
        '2024-05-20 09:00:00',
        '2024-05-20 09:00:00'
    ),
    -- Bác sĩ 15: Chuyên khoa vật lý trị liệu
    (
        '木村医師',
        'リハビリテーション科・理学療法科',
        '03-5678-9013',
        'kimura@clinic.example.com',
        'active',
        '2024-06-01 09:00:00',
        '2024-06-01 09:00:00'
    );
-- Hiển thị kết quả
SELECT id,
    name,
    specialization,
    phone,
    email,
    status,
    created_at
FROM wp_pcb_doctors
ORDER BY created_at ASC;
