<?php

/**
 * Process handler - memproses request enkripsi/dekripsi
 * Menerima POST request dan mengembalikan JSON response
 */

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Include cipher files
require_once __DIR__ . '/ciphers/vigenere.php';
require_once __DIR__ . '/ciphers/affine.php';
require_once __DIR__ . '/ciphers/playfair.php';
require_once __DIR__ . '/ciphers/hill.php';
require_once __DIR__ . '/ciphers/enigma.php';

$cipher = $_POST['cipher'] ?? '';
$action = $_POST['action'] ?? '';
$text = $_POST['text'] ?? '';

if (empty($text)) {
    echo json_encode(['error' => 'Teks tidak boleh kosong.']);
    exit;
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
            $rotors = [];
            $rotors[] = $_POST['rotor_right'] ?? 'I';
            $rotors[] = $_POST['rotor_middle'] ?? 'II';
            $rotors[] = $_POST['rotor_left'] ?? 'III';

            $positions = [];
            $positions[] = intval($_POST['pos_right'] ?? 0);
            $positions[] = intval($_POST['pos_middle'] ?? 0);
            $positions[] = intval($_POST['pos_left'] ?? 0);

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

    echo json_encode([
        'success' => true,
        'result' => $result,
        'cipher' => $cipher,
        'action' => $action,
    ]);

} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage(),
    ]);
}
