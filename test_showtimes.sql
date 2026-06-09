-- Test query để kiểm tra dữ liệu showtimes
-- Chạy trong phpMyAdmin hoặc MySQL client

-- 1. Kiểm tra có bao nhiêu showtimes
SELECT COUNT(*) as total_showtimes FROM showtimes;

-- 2. Kiểm tra showtimes của hôm nay trở đi
SELECT 
    s.*,
    m.title as movie_title,
    t.name as theater_name,
    sc.screen_name,
    sc.screen_type
FROM showtimes s
LEFT JOIN movies m ON s.movie_id = m.id
LEFT JOIN theaters t ON s.theater_id = t.id
LEFT JOIN theater_screens sc ON s.screen_id = sc.id
WHERE s.show_date >= CURDATE()
ORDER BY s.show_date, s.show_time;

-- 3. Kiểm tra showtimes theo từng phim
SELECT 
    m.title as movie_title,
    COUNT(s.id) as showtime_count
FROM movies m
LEFT JOIN showtimes s ON m.id = s.movie_id AND s.show_date >= CURDATE()
WHERE m.status = 'Chiếu rạp'
GROUP BY m.id, m.title;

-- 4. Kiểm tra showtimes theo từng rạp
SELECT 
    t.name as theater_name,
    m.title as movie_title,
    s.show_date,
    s.show_time,
    sc.screen_name
FROM theaters t
LEFT JOIN showtimes s ON t.id = s.theater_id AND s.show_date >= CURDATE()
LEFT JOIN movies m ON s.movie_id = m.id
LEFT JOIN theater_screens sc ON s.screen_id = sc.id
ORDER BY t.name, s.show_date, s.show_time;

-- 5. Kiểm tra relationship giữa showtimes và screens
SELECT 
    s.id,
    s.show_date,
    s.show_time,
    s.screen_id,
    sc.screen_name,
    sc.screen_type,
    CASE 
        WHEN sc.id IS NULL THEN 'Missing Screen'
        ELSE 'OK'
    END as screen_status
FROM showtimes s
LEFT JOIN theater_screens sc ON s.screen_id = sc.id
WHERE s.show_date >= CURDATE()
LIMIT 20;
