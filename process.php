<?php

/**
 * Process handler - memproses request enkripsi/dekripsi
 * Menerima POST request dan mengembalikan JSON response atau file download
 */

header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Include cipher files
require_once __DIR__ . '/ciphers/vigenere.php';
require_once __DIR__ . '/ciphers/affine.php';
require_once __DIR__ . '/ciphers/playfair.php';
require_once __DIR__ . '/ciphers/hill.php';
require_once __DIR__ . '/ciphers/enigma.php';

// Fungsi bantuan untuk mapping Hex ke Alfabet (A-I, K-Q untuk menghindari J di Playfair)
function mapHexToAP($hexStr) {
    $hexStr = strtolower($hexStr);
    $map = [
        '0'=>'A', '1'=>'B', '2'=>'C', '3'=>'D', '4'=>'E',
        '5'=>'F', '6'=>'G', '7'=>'H', '8'=>'I', '9'=>'K',
        'a'=>'L', 'b'=>'M', 'c'=>'N', 'd'=>'O', 'e'=>'P', 'f'=>'Q'
    ];
    return strtr($hexStr, $map);
}

// reverse mapping
function mapAPToHex($apStr) {
    $apStr = strtoupper($apStr);
    $map = [
        'A'=>'0', 'B'=>'1', 'C'=>'2', 'D'=>'3', 'E'=>'4',
        'F'=>'5', 'G'=>'6', 'H'=>'7', 'I'=>'8', 'K'=>'9',
        'L'=>'a', 'M'=>'b', 'N'=>'c', 'O'=>'d', 'P'=>'e', 'Q'=>'f'
    ];
    return strtr($apStr, $map);
}

$cipher = $_POST['cipher'] ?? '';
$action = $_POST['action'] ?? '';
$inputType = $_POST['inputType'] ?? 'text';
$text = '';
$originalName = '';

if ($inputType === 'file') {
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Gagal mengunggah file. Pastikan file valid dan ukuran tidak melebihi batas.']);
        exit;
    }
    
    // Batasi maksimum ukuran file 2 MB untuk mencegah memory exhaustion (Fatal Error)
    if ($_FILES['file']['size'] > 2 * 1024 * 1024) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Ukuran file terlalu besar. Batas maksimal adalah 2 MB untuk menghindari kehabisan memori server.']);
        exit;
    }
    
    $fileContent = file_get_contents($_FILES['file']['tmp_name']);
    if ($fileContent === false) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Gagal membaca isi file.']);
        exit;
    }
    
    if ($action === 'encrypt') {
        $originalName = $_FILES['file']['name'];
        $hex = bin2hex($fileContent);
        $text = mapHexToAP($hex); // Jadikan teks
    } else {
        // Pada saat dekripsi, file tersebut adalah file .txt hasil tangkapan sebelumnya
        $lines = explode("\n", str_replace("\r", "", $fileContent), 2);
        if (count($lines) < 2 || strpos($lines[0], 'original_filename:') !== 0) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Format file tidak valid untuk dekripsi. Harus .txt hasil enkripsi.']);
            exit;
        }
        $originalName = trim(str_replace('original_filename:', '', $lines[0]));
        $text = trim($lines[1]);
        if (empty($originalName)) {
            $originalName = 'decrypted_file.bin';
        }
    }
} else {
    $text = $_POST['text'] ?? '';
    if (empty($text)) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Teks tidak boleh kosong.']);
        exit;
    }
}

try {
    $result = '';

    switch ($cipher) {
        case 'vigenere':
            $key = $_POST['key'] ?? '';
            if (empty($key)) {
                throw new InvalidArgumentException('Key tidak boleh kosong.');
            }
            $result = ($action === 'encrypt')
                ? vigenere_encrypt($text, $key)
                : vigenere_decrypt($text, $key);
            break;

        case 'affine':
            $a = intval($_POST['a'] ?? 0);
            $b = intval($_POST['b'] ?? 0);
            if ($a === 0) {
                throw new InvalidArgumentException('Nilai a tidak boleh 0.');
            }
            $result = ($action === 'encrypt')
                ? affine_encrypt($text, $a, $b)
                : affine_decrypt($text, $a, $b);
            break;

        case 'playfair':
            $key = $_POST['key'] ?? '';
            if (empty($key)) {
                throw new InvalidArgumentException('Key tidak boleh kosong.');
            }
            $result = ($action === 'encrypt')
                ? playfair_encrypt($text, $key)
                : playfair_decrypt($text, $key);
            break;

        case 'hill':
            $key = $_POST['key'] ?? '';
            if (empty($key)) {
                throw new InvalidArgumentException('Key tidak boleh kosong.');
            }
            $result = ($action === 'encrypt')
                ? hill_encrypt($text, $key)
                : hill_decrypt($text, $key);
            break;

        case 'enigma':
            $rotors = [$_POST['rotor_right'] ?? 'I', $_POST['rotor_middle'] ?? 'II', $_POST['rotor_left'] ?? 'III'];
            $positions = [
                intval($_POST['pos_right'] ?? 0),
                intval($_POST['pos_middle'] ?? 0),
                intval($_POST['pos_left'] ?? 0)
            ];
            $reflector = $_POST['reflector'] ?? 'B';
            $plugboard = $_POST['plugboard'] ?? '';

            $config = [
                'rotors' => $rotors,
                'reflector' => $reflector,
                'positions' => $positions,
                'plugboard' => $plugboard,
            ];

            $result = ($action === 'encrypt')
                ? enigma_encrypt($text, $config)
                : enigma_decrypt($text, $config);
            break;

        default:
            throw new InvalidArgumentException('Cipher tidak valid.');
    }

    if ($inputType === 'file') {
        if ($action === 'encrypt') {
            // Gabungkan original_filename di pass baris pertama (menghindari error decode nanti)
            $outputContent = "original_filename:" . $originalName . "\n" . $result;
            $downloadName = pathinfo($originalName, PATHINFO_FILENAME) . '_encrypted.txt';
            
            header('Content-Type: text/plain');
            header('Content-Disposition: attachment; filename="' . $downloadName . '"');
            header('Content-Length: ' . strlen($outputContent));
            echo $outputContent;
        } else {
            // Decrypt File ke binary aslinya
            $hexText = mapAPToHex($result);

            // [FIX] Bersihkan karakter padding (seperti 'X' dari Hill Cipher) dan pastikan panjangnya genap
            $hexText = preg_replace('/[^0-9a-fA-F]/', '', $hexText);
            if (strlen($hexText) % 2 !== 0) {
                $hexText = substr($hexText, 0, -1);
            }

            $binaryContent = hex2bin($hexText);
            
            if ($binaryContent === false) {
                 header('Content-Type: application/json');
                 echo json_encode(['error' => 'Gagal mengubah hex kembali ke biner. Ciphertext rusak.']);
                 exit;
            }
            
            // Mencoba mendeteksi mime jika memungkinkan, tapi octet-stream aman default
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $originalName . '"');
            header('Content-Length: ' . strlen($binaryContent));
            echo $binaryContent;
        }
        exit;
    }

    // Output default JSON array untuk respons Input Teks
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'result' => $result,
        'cipher' => $cipher,
        'action' => $action,
    ]);

} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'error' => $e->getMessage(),
    ]);
}
