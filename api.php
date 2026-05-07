<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    


    $query = $input['query'] ?? '';

    if (empty($query)) {
        echo json_encode(['error' => 'Soru boş olamaz.']);
        exit;
    }

    $notebook_id = '846e5e5f-408a-4067-b8c2-1ed7df0838fc';
    $clean_query = escapeshellarg($query);
    
    // --- CACHE (ÖNBELLEK) SİSTEMİ BAŞLANGICI ---
    $cache_dir = __DIR__ . '/cache';
    if (!is_dir($cache_dir)) {
        mkdir($cache_dir, 0777, true);
    }
    
    // Soruyu md5 ile şifreleyip benzersiz bir dosya adı oluşturuyoruz (harf büyüklüğü duyarsız yapmak için strtolower kullanıyoruz)
    $cache_key = md5(strtolower(trim($query)));
    $cache_file = $cache_dir . '/' . $cache_key . '.txt';
    
    // Eğer önbellek dosyası varsa ve 7 günden eskiyse geçerliliğini korusun
    if (file_exists($cache_file)) {
        $cached_response = file_get_contents($cache_file);
        echo json_encode(['response' => $cached_response, 'cached' => true]);
        exit;
    }
    // --- CACHE (ÖNBELLEK) SİSTEMİ BİTİŞİ ---

    // nlm komutunu çalıştır
    $command = "set USERPROFILE=C:\\Users\\asus&& C:\\Users\\asus\\.local\\bin\\nlm.EXE query notebook $notebook_id $clean_query 2>&1";
    
    $output = shell_exec($command);
    
    // Oturum (Auth) hatasını yakala
    if ($output !== null && (strpos($output, 'Authentication expired') !== false || strpos($output, 'Profile \'default\' not found') !== false || strpos($output, 'Run \'nlm login\'') !== false)) {
        echo json_encode(['error' => 'Oturum süresi doldu.', 'auth_required' => true]);
        exit;
    }
    
    if ($output === null) {
        echo json_encode(['error' => 'Komut çalıştırılamadı.']);
    } else {
        $json_start = strpos($output, '{');
        if ($json_start !== false) {
            $json_content = substr($output, $json_start);
            $decoded = json_decode($json_content, true);
            
            $final_answer = "";
            
            if (isset($decoded['value']['answer'])) {
                $final_answer = $decoded['value']['answer'];
            } elseif (isset($decoded['answer'])) {
                $final_answer = $decoded['answer'];
            } elseif (isset($decoded['response']['answer'])) {
                $final_answer = $decoded['response']['answer'];
            } else {
                $final_answer = $output;
            }
            
            $final_answer = preg_replace('/\[[\d\s,\-]+\]/', '', $final_answer);
            $final_answer = preg_replace('/\s+([.,!?])/', '$1', $final_answer);
            $final_answer = trim($final_answer);
            
            // Başarılı cevabı cache (önbellek) dosyasına kaydet
            if (!empty($final_answer)) {
                file_put_contents($cache_file, $final_answer);
            }
            
            echo json_encode(['response' => $final_answer, 'cached' => false]);
        } else {
            $cleaned_output = preg_replace('/Background command ID: .*/', '', $output);
            $cleaned_output = preg_replace('/\[[\d\s,\-]+\]/', '', $cleaned_output);
            $cleaned_output = preg_replace('/\s+([.,!?])/', '$1', $cleaned_output);
            $cleaned_output = trim($cleaned_output);
            
            // Çıktıyı kaydet
            if (!empty($cleaned_output)) {
                file_put_contents($cache_file, $cleaned_output);
            }
            
            echo json_encode(['response' => $cleaned_output, 'cached' => false]);
        }
    }
} else {
    echo json_encode(['error' => 'Sadece POST istekleri kabul edilir.']);
}
