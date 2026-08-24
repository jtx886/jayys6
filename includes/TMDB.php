&lt;?php
class TMDB {
    private $apiKey;
    private $baseUrl = 'https://api.themoviedb.org/3';
    private $imageBase = 'https://image.tmdb.org/t/p';
    private $language = 'zh-CN';
    
    public function __construct() {
        global $db;
        $setting = $db-&gt;fetch("SELECT setting_value FROM settings WHERE setting_key = 'tmdb_api_key'");
        $this-&gt;apiKey = $setting ? $setting['setting_value'] : TMDB_API_KEY;
    }
    
    private function request($endpoint, $params = []) {
        $params['api_key'] = $this-&gt;apiKey;
        $params['language'] = $this-&gt;language;
        
        $url = $this-&gt;baseUrl . $endpoint . '?' . http_build_query($params);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }
    
    public function getTrending($type = 'all', $timeWindow = 'week') {
        return $this-&gt;request("/trending/$type/$timeWindow");
    }
    
    public function getPopular($type = 'movie', $page = 1) {
        return $this-&gt;request("/$type/popular", ['page' =&gt; $page]);
    }
    
    public function getTopRated($type = 'movie', $page = 1) {
        return $this-&gt;request("/$type/top_rated", ['page' =&gt; $page]);
    }
    
    public function getNowPlaying($page = 1) {
        return $this-&gt;request('/movie/now_playing', ['page' =&gt; $page]);
    }
    
    public function getAiringToday($page = 1) {
        return $this-&gt;request('/tv/airing_today', ['page' =&gt; $page]);
    }
    
    public function getDetails($type, $id) {
        return $this-&gt;request("/$type/$id", [
            'append_to_response' =&gt; 'credits,videos,similar,recommendations,images,alternative_titles,translations,external_ids'
        ]);
    }
    
    public function getSeasonDetails($tvId, $seasonNumber) {
        return $this-&gt;request("/tv/$tvId/season/$seasonNumber");
    }
    
    public function search($query, $type = 'multi', $page = 1) {
        return $this-&gt;request("/search/$type", [
            'query' =&gt; $query,
            'page' =&gt; $page,
            'include_adult' =&gt; false
        ]);
    }
    
    public function getGenres($type = 'movie') {
        return $this-&gt;request("/genre/$type/list");
    }
    
    public function getByGenre($type, $genreId, $page = 1) {
        return $this-&gt;request("/discover/$type", [
            'with_genres' =&gt; $genreId,
            'page' =&gt; $page,
            'sort_by' =&gt; 'popularity.desc'
        ]);
    }
    
    public function getImageUrl($path, $size = 'w500') {
        if (!$path) return '';
        return $this-&gt;imageBase . '/' . $size . $path;
    }
    
    public function getBackdropUrl($path, $size = 'original') {
        return $this-&gt;getImageUrl($path, $size);
    }
    
    // 检查是否有普通话配音
    public function hasMandarin($data) {
        if (!isset($data['translations']['translations'])) return false;
        foreach ($data['translations']['translations'] as $t) {
            if (in_array($t['iso_3166_1'], ['CN', 'TW', 'HK']) || in_array($t['iso_639_1'], ['zh', 'cmn'])) {
                return true;
            }
        }
        return false;
    }
    
    // 获取年份
    public function getYear($media) {
        if (isset($media['release_date']) &amp;&amp; $media['release_date']) {
            return substr($media['release_date'], 0, 4);
        }
        if (isset($media['first_air_date']) &amp;&amp; $media['first_air_date']) {
            return substr($media['first_air_date'], 0, 4);
        }
        return '';
    }
}
?&gt;
