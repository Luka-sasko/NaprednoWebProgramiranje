<?php
require_once 'iRadovi.php';
require_once 'simple_html_dom.php';

class DiplomskiRadovi implements iRadovi {
    private $naziv_rada;
    private $tekst_rada;
    private $link_rada;
    private $oib_tvrtke;
    private $db;

    public function __construct() {
        // Database connection using PDO
        try {
            $this->db = new PDO('mysql:host=localhost;dbname=radovi;charset=utf8mb4', 'root', '');
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public function create(string $naziv_rada, string $tekst_rada, string $link_rada, string $oib_tvrtke): void {
        $this->naziv_rada = $naziv_rada;
        $this->tekst_rada = $tekst_rada;
        $this->link_rada = $link_rada;
        $this->oib_tvrtke = $oib_tvrtke;
    }

    public function save(): void {
        try {
            $stmt = $this->db->prepare("INSERT INTO diplomski_radovi (naziv_rada, tekst_rada, link_rada, oib_tvrtke) VALUES (:naziv, :tekst, :link, :oib)");
            $stmt->execute([
                ':naziv' => $this->naziv_rada,
                ':tekst' => $this->tekst_rada,
                ':link' => $this->link_rada,
                ':oib' => $this->oib_tvrtke
            ]);
            echo "Saved: {$this->naziv_rada}<br>";
        } catch (PDOException $e) {
            echo "Error saving to database: " . $e->getMessage() . "<br>";
        }
    }

    public function read(): array {
        try {
            $stmt = $this->db->query("SELECT * FROM diplomski_radovi");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error reading from database: " . $e->getMessage() . "<br>";
            return [];
        }
    }

    public function fetchDataFromPage(int $redni_broj): void {
        $base_url = "https://stup.ferit.hr";
        $url = "$base_url/index.php/zavrsni-radovi/page/$redni_broj/";

        // Fetch the page using cURL
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        ]);

        $html = curl_exec($ch);
        if ($html === false) {
            echo "Failed to fetch page: $url - " . curl_error($ch) . "<br>";
            curl_close($ch);
            return;
        }
        curl_close($ch);

        // Parse the HTML
        $dom = new simple_html_dom();
        $dom->load($html);

        // Find all thesis entries
        $posts = $dom->find('article.fusion-post-medium');
        if (empty($posts)) {
            echo "No posts found on page: $url.<br>";
            $dom->clear();
            return;
        }

        echo "Found " . count($posts) . " posts on page $redni_broj.<br>";

        foreach ($posts as $index => $post) {
            echo "<h3>Processing Post #$index on Page $redni_broj</h3>";

            // Extract title
            $naziv_rada_elem = $post->find('h2.blog-shortcode-post-title a', 0);
            $naziv_rada = $naziv_rada_elem ? trim($naziv_rada_elem->plaintext) : "No title found";
            echo "Title: $naziv_rada<br>";

            // Extract link
            $link_rada = $naziv_rada_elem ? trim($naziv_rada_elem->href) : "No link found";
            if ($link_rada && strpos($link_rada, 'http') !== 0) {
                $link_rada = $base_url . (strpos($link_rada, '/') === 0 ? $link_rada : '/' . $link_rada);
            }
            echo "Link: $link_rada<br>";

            // Extract OIB from image
            $oib_tvrtke_elem = $post->find('div.fusion-image-wrapper img[src*=logos]', 0);
            $oib_tvrtke = $oib_tvrtke_elem ? $this->extractOIBFromImage($oib_tvrtke_elem->src) : "No OIB found";
            echo "OIB: $oib_tvrtke<br>";

            // Extract text from the linked page
            $tekst_rada = "No text available.";
            if ($link_rada && filter_var($link_rada, FILTER_VALIDATE_URL)) {
                $ch = curl_init($link_rada);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT => 10,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                ]);
                $linked_html = curl_exec($ch);
                if ($linked_html === false) {
                    echo "Failed to fetch linked page: $link_rada - " . curl_error($ch) . "<br>";
                } else {
                    $linked_dom = new simple_html_dom();
                    $linked_dom->load($linked_html);

                    $paragraphs = $linked_dom->find('div.entry-content p');
                    $text_parts = [];
                    foreach ($paragraphs as $p) {
                        $text = trim(strip_tags($p->innertext));
                        if (!empty($text)) {
                            $text_parts[] = $text;
                        }
                    }
                    $tekst_rada = !empty($text_parts) ? implode("\n", $text_parts) : "No content found.";
                    $linked_dom->clear();
                }
                curl_close($ch);
            } else {
                echo "Invalid or missing URL for text extraction: $link_rada<br>";
            }
            echo "Text: $tekst_rada<br>";

            // Create and save the object
            $this->create($naziv_rada, $tekst_rada, $link_rada, $oib_tvrtke);
            $this->save();

            echo "<hr>";
        }

        $dom->clear(); // Free memory
    }

    private function extractOIBFromImage(string $src): string {
        if (strpos($src, "logos/") !== false) {
            $parts = explode("logos/", $src);
            $filename = isset($parts[1]) ? $parts[1] : "";
            $oib = substr($filename, 0, strpos($filename, '.png'));
            return $oib ?: "No OIB in image src";
        }
        return "No OIB in image src: $src";
    }
}