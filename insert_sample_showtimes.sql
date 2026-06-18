-- Script để insert dữ liệu mẫu cho showtimes
-- Chạy script này nếu chưa có dữ liệu showtime trong database

-- Lấy movie_id, theater_id, screen_id từ database hiện tại
-- Bạn cần thay đổi các ID này theo dữ liệu thực tế trong database

-- Ví dụ: Insert showtimes cho 7 ngày tới
-- Giả sử:
-- - movie_id = 1 (phim đầu tiên)
-- - theater_id = 1 (rạp đầu tiên)
-- - screen_id = 1 (phòng chiếu đầu tiên)

-- Ngày hôm nay
INSERT INTO showtimes (movie_id, theater_id, screen_id, show_date, show_time, price, available_seats, created_at)
SELECT 
    m.id as movie_id,
    t.id as theater_id,
    s.id as screen_id,
    CURDATE() as show_date,
    '10:00:00' as show_time,
    90000 as price,
    100 as available_seats,
    NOW() as created_at
FROM movies m
CROSS JOIN theaters t
CROSS JOIN theater_screens s
WHERE m.status = 'Chiếu rạp'
    AND s.theater_id = t.id
LIMIT 1;

INSERT INTO showtimes (movie_id, theater_id, screen_id, show_date, show_time, price, available_seats, created_at)
SELECT 
    m.id as movie_id,
    t.id as theater_id,
    s.id as screen_id,
    CURDATE() as show_date,
    '14:00:00' as show_time,
    90000 as price,
    100 as available_seats,
    NOW() as created_at
FROM movies m
CROSS JOIN theaters t
CROSS JOIN theater_screens s
WHERE m.status = 'Chiếu rạp'
    AND s.theater_id = t.id
LIMIT 1;

INSERT INTO showtimes (movie_id, theater_id, screen_id, show_date, show_time, price, available_seats, created_at)
SELECT 
    m.id as movie_id,
    t.id as theater_id,
    s.id as screen_id,
    CURDATE() as show_date,
    '18:00:00' as show_time,
    90000 as price,
    100 as available_seats,
    NOW() as created_at
FROM movies m
CROSS JOIN theaters t
CROSS JOIN theater_screens s
WHERE m.status = 'Chiếu rạp'
    AND s.theater_id = t.id
LIMIT 1;

INSERT INTO showtimes (movie_id, theater_id, screen_id, show_date, show_time, price, available_seats, created_at)
SELECT 
    m.id as movie_id,
    t.id as theater_id,
    s.id as screen_id,
    CURDATE() as show_date,
    '21:00:00' as show_time,
    90000 as price,
    100 as available_seats,
    NOW() as created_at
FROM movies m
CROSS JOIN theaters t
CROSS JOIN theater_screens s
WHERE m.status = 'Chiếu rạp'
    AND s.theater_id = t.id
LIMIT 1;

-- Ngày mai
INSERT INTO showtimes (movie_id, theater_id, screen_id, show_date, show_time, price, available_seats, created_at)
SELECT 
    m.id as movie_id,
    t.id as theater_id,
    s.id as screen_id,
    DATE_ADD(CURDATE(), INTERVAL 1 DAY) as show_date,
    '10:00:00' as show_time,
    90000 as price,
    100 as available_seats,
    NOW() as created_at
FROM movies m
CROSS JOIN theaters t
CROSS JOIN theater_screens s
WHERE m.status = 'Chiếu rạp'
    AND s.theater_id = t.id
LIMIT 1;

INSERT INTO showtimes (movie_id, theater_id, screen_id, show_date, show_time, price, available_seats, created_at)
SELECT 
    m.id as movie_id,
    t.id as theater_id,
    s.id as screen_id,
    DATE_ADD(CURDATE(), INTERVAL 1 DAY) as show_date,
    '14:00:00' as show_time,
    90000 as price,
    100 as available_seats,
    NOW() as created_at
FROM movies m
CROSS JOIN theaters t
CROSS JOIN theater_screens s
WHERE m.status = 'Chiếu rạp'
    AND s.theater_id = t.id
LIMIT 1;

-- Kiểm tra kết quả
SELECT 
    s.id,
    m.title as movie,
    t.name as theater,
    sc.screen_name,
    s.show_date,
    s.show_time,
    s.price
FROM showtimes s
JOIN movies m ON s.movie_id = m.id
JOIN theaters t ON s.theater_id = t.id
JOIN theater_screens sc ON s.screen_id = sc.id
WHERE s.show_date >= CURDATE()
ORDER BY s.show_date, s.show_time;
