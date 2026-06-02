<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/MovieModel.php';

class HomeController extends Controller {
    
    public function index() {
        $movieModel = new MovieModel();
        
        // Lấy slider phim nổi bật - ưu tiên phim có banner
        // Mix cả phim lẻ và phim bộ, ưu tiên phim có rating cao và có banner
        $sliderMovies = $movieModel->getDb()->fetchAll("
            SELECT m.*, c.name as category_name 
            FROM movies m 
            LEFT JOIN categories c ON m.category_id = c.id 
            WHERE m.status = 'Chiếu online' 
            AND m.status_admin = 'published'
            AND (m.banner IS NOT NULL AND m.banner != '' OR m.thumbnail IS NOT NULL AND m.thumbnail != '')
            ORDER BY 
                CASE WHEN m.banner IS NOT NULL AND m.banner != '' THEN 1 ELSE 2 END,
                m.rating DESC, 
                RAND()
            LIMIT 5
        ");
        
        // Nếu không đủ 5 phim, lấy thêm từ phim khác (không bao gồm phim chiếu rạp)
        if (count($sliderMovies) < 5) {
            $additionalMovies = $movieModel->getDb()->fetchAll("
                SELECT m.*, c.name as category_name 
                FROM movies m 
                LEFT JOIN categories c ON m.category_id = c.id 
                WHERE m.status != 'Chiếu rạp'
                AND m.status_admin = 'published'
                AND m.thumbnail IS NOT NULL 
                AND m.thumbnail != ''
                AND m.id NOT IN (" . (!empty($sliderMovies) ? implode(',', array_column($sliderMovies, 'id')) : '0') . ")
                ORDER BY m.rating DESC, RAND()
                LIMIT " . (5 - count($sliderMovies)) . "
            ");
            $sliderMovies = array_merge($sliderMovies, $additionalMovies);
        }
        
        // Shuffle để random thứ tự hiển thị
        if (!empty($sliderMovies)) {
            shuffle($sliderMovies);
        }
        
        // Lấy phim lẻ và phim bộ riêng biệt cho section (không bao gồm phim chiếu rạp)
        $phimLe = $movieModel->getDb()->fetchAll("
            SELECT m.*, c.name as category_name 
            FROM movies m 
            LEFT JOIN categories c ON m.category_id = c.id 
            WHERE (m.type = 'phimle' OR m.type IS NULL)
            AND m.status != 'Chiếu rạp'
            AND m.status_admin = 'published'
            ORDER BY m.rating DESC, m.created_at DESC 
            LIMIT 8
        ");
        
        $phimBo = $movieModel->getDb()->fetchAll("
            SELECT m.*, c.name as category_name 
            FROM movies m 
            LEFT JOIN categories c ON m.category_id = c.id 
            WHERE m.type = 'phimbo'
            AND m.status != 'Chiếu rạp'
            AND m.status_admin = 'published'
            ORDER BY m.rating DESC, m.created_at DESC 
            LIMIT 8
        ");
        
        // Phim mới nhất - cả phim lẻ và phim bộ (không bao gồm phim chiếu rạp)
        $latestMovies = $movieModel->getDb()->fetchAll("
            SELECT m.*, c.name as category_name 
            FROM movies m 
            LEFT JOIN categories c ON m.category_id = c.id 
            WHERE m.status != 'Chiếu rạp'
            AND m.status_admin = 'published'
            ORDER BY m.created_at DESC 
            LIMIT 12
        ");
        
        // Lấy danh sách favorites của user nếu đã đăng nhập
        $favorites = [];
        $user = $this->getCurrentUser();
        if ($user) {
            require_once __DIR__ . '/../models/WatchHistoryModel.php';
            $favoriteMovies = $movieModel->getDb()->fetchAll("
                SELECT movie_id 
                FROM watch_history 
                WHERE user_id = ? AND favorite = 1
            ", [$user['id']]);
            $favorites = array_column($favoriteMovies, 'movie_id');
        }
        
        // Top phim được xem nhiều nhất trong tuần (dựa trên watch_history)
        $topMoviesWeek = $movieModel->getDb()->fetchAll("
            SELECT m.*, c.name as category_name, COUNT(wh.id) as view_count,
                   (SELECT COUNT(*) FROM episodes e WHERE e.movie_id = m.id) as episode_count
            FROM movies m 
            LEFT JOIN categories c ON m.category_id = c.id 
            LEFT JOIN watch_history wh ON m.id = wh.movie_id AND wh.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            WHERE m.status != 'Chiếu rạp'
            AND m.status_admin = 'published'
            GROUP BY m.id
            ORDER BY view_count DESC, m.rating DESC
            LIMIT 10
        ");
        
        // Thêm số tập thực tế cho phim bộ
        foreach ($phimBo as &$movie) {
            $episodeCount = $movieModel->getDb()->fetch("SELECT COUNT(*) as count FROM episodes WHERE movie_id = ?", [$movie['id']]);
            $movie['episode_count'] = $episodeCount ? $episodeCount['count'] : 0;
        }
        unset($movie);
        
        // Thêm số tập thực tế cho phim mới nhất (nếu là phim bộ)
        foreach ($latestMovies as &$movie) {
            if (($movie['type'] ?? 'phimle') === 'phimbo') {
                $episodeCount = $movieModel->getDb()->fetch("SELECT COUNT(*) as count FROM episodes WHERE movie_id = ?", [$movie['id']]);
                $movie['episode_count'] = $episodeCount ? $episodeCount['count'] : 0;
            }
        }
        unset($movie);
        
        $this->view('home/index', [
            'sliderMovies' => $sliderMovies,
            'latestMovies' => $latestMovies,
            'phimLe' => $phimLe,
            'phimBo' => $phimBo,
            'topMoviesWeek' => $topMoviesWeek,
            'user' => $user,
            'favorites' => $favorites
        ]);
    }
}
?>

