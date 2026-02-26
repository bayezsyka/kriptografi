<?php

/**
 * Enigma Cipher - Simplified simulation
 * 
 * Simulasi mesin Enigma dengan:
 * - 3 rotor yang bisa dipilih (I, II, III, IV, V)
 * - Reflektor (B atau C)
 * - Plugboard (pasangan huruf)
 * - Posisi awal rotor
 * 
 * Catatan: Ini adalah simulasi yang disederhanakan dari mesin Enigma M3.
 */

// Wiring rotor historis Enigma
$ENIGMA_ROTORS = [
    'I'   => 'EKMFLGDQVZNTOWYHXUSPAIBRCJ',
    'II'  => 'AJDKSIRUXBLHWTMCQGZNPYFVOE',
    'III' => 'BDFHJLCPRTXVZNYEIWGAKMUSQO',
    'IV'  => 'ESOVPZJAYQUIRHXLNFTGKDCMWB',
    'V'   => 'VZBRGITYUPSDNHLXAWMJQOFECK',
];

// Notch positions (turnover)
$ENIGMA_NOTCHES = [
    'I'   => 'Q',
    'II'  => 'E',
    'III' => 'V',
    'IV'  => 'J',
    'V'   => 'Z',
];

// Reflektor
$ENIGMA_REFLECTORS = [
    'B' => 'YRUHQSLDPXNGOKMIEBFZCWVJAT',
    'C' => 'FVPJIAOYEDRZXWGCTKUQSBNMHL',
];

function enigma_char_to_num(string $ch): int
{
    return ord(strtoupper($ch)) - ord('A');
}

function enigma_num_to_char(int $n): int
{
    return $n;
}

function enigma_apply_plugboard(int $ch, array $plugboard): int
{
    return $plugboard[$ch] ?? $ch;
}

function enigma_parse_plugboard(string $plugboardStr): array
{
    $plugboard = [];
    $plugboardStr = strtoupper(trim($plugboardStr));

    if (empty($plugboardStr)) {
        return $plugboard;
    }

    $pairs = preg_split('/[\s,]+/', $plugboardStr);
    foreach ($pairs as $pair) {
        $pair = preg_replace('/[^A-Z]/', '', $pair);
        if (strlen($pair) === 2) {
            $a = ord($pair[0]) - ord('A');
            $b = ord($pair[1]) - ord('A');
            $plugboard[$a] = $b;
            $plugboard[$b] = $a;
        }
    }

    return $plugboard;
}

function enigma_process(string $text, array $config): string
{
    global $ENIGMA_ROTORS, $ENIGMA_NOTCHES, $ENIGMA_REFLECTORS;

    $text = strtoupper(preg_replace('/[^a-zA-Z]/', '', $text));

    // Parse konfigurasi
    $rotorNames = $config['rotors'] ?? ['I', 'II', 'III']; // kanan ke kiri: [right, middle, left]
    $reflectorName = $config['reflector'] ?? 'B';
    $positions = $config['positions'] ?? [0, 0, 0]; // posisi awal [right, middle, left]
    $plugboard = enigma_parse_plugboard($config['plugboard'] ?? '');

    // Validasi
    foreach ($rotorNames as $name) {
        if (!isset($ENIGMA_ROTORS[$name])) {
            throw new InvalidArgumentException("Rotor '$name' tidak valid. Pilih dari: I, II, III, IV, V.");
        }
    }
    if (!isset($ENIGMA_REFLECTORS[$reflectorName])) {
        throw new InvalidArgumentException("Reflektor '$reflectorName' tidak valid. Pilih dari: B, C.");
    }

    // Siapkan wiring
    $rotorWirings = [];
    $notches = [];
    foreach ($rotorNames as $name) {
        $rotorWirings[] = $ENIGMA_ROTORS[$name];
        $notches[] = ord($ENIGMA_NOTCHES[$name]) - ord('A');
    }
    $reflector = $ENIGMA_REFLECTORS[$reflectorName];

    // Posisi rotor (mutable)
    $pos = $positions;

    $result = '';

    for ($i = 0; $i < strlen($text); $i++) {
        // Step rotors (sebelum enkripsi)
        // Double stepping: middle rotor step jika middle di notch
        $middleAtNotch = ($pos[1] === $notches[1]);
        $rightAtNotch = ($pos[0] === $notches[0]);

        // Right rotor selalu berputar
        $pos[0] = ($pos[0] + 1) % 26;

        // Middle rotor berputar jika right di notch ATAU middle di notch (double stepping)
        if ($rightAtNotch || $middleAtNotch) {
            $pos[1] = ($pos[1] + 1) % 26;
        }

        // Left rotor berputar jika middle di notch
        if ($middleAtNotch) {
            $pos[2] = ($pos[2] + 1) % 26;
        }

        $ch = ord($text[$i]) - ord('A');

        // Plugboard
        $ch = enigma_apply_plugboard($ch, $plugboard);

        // Forward melalui rotor (kanan → tengah → kiri)
        for ($r = 0; $r < 3; $r++) {
            $ch = ($ch + $pos[$r]) % 26;
            $ch = ord($rotorWirings[$r][$ch]) - ord('A');
            $ch = ($ch - $pos[$r] + 26) % 26;
        }

        // Reflektor
        $ch = ord($reflector[$ch]) - ord('A');

        // Backward melalui rotor (kiri → tengah → kanan)
        for ($r = 2; $r >= 0; $r--) {
            $ch = ($ch + $pos[$r]) % 26;
            // Inverse wiring
            $wiring = $rotorWirings[$r];
            for ($j = 0; $j < 26; $j++) {
                if (ord($wiring[$j]) - ord('A') === $ch) {
                    $ch = $j;
                    break;
                }
            }
            $ch = ($ch - $pos[$r] + 26) % 26;
        }

        // Plugboard lagi
        $ch = enigma_apply_plugboard($ch, $plugboard);

        $result .= chr($ch + ord('A'));
    }

    return $result;
}

function enigma_encrypt(string $plaintext, array $config): string
{
    return enigma_process($plaintext, $config);
}

function enigma_decrypt(string $ciphertext, array $config): string
{
    // Enigma bersifat reciprocal: encrypt = decrypt dengan config yang sama
    return enigma_process($ciphertext, $config);
}
