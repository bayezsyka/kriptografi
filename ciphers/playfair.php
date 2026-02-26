<?php

/**
 * Playfair Cipher - 26 huruf alfabet (J digabung ke I)
 */

function playfair_generate_matrix(string $key): array
{
    $key = strtoupper(preg_replace('/[^a-zA-Z]/', '', $key));
    $key = str_replace('J', 'I', $key);

    $seen = [];
    $matrix = [];

    // Tambahkan huruf dari key
    for ($i = 0; $i < strlen($key); $i++) {
        $ch = $key[$i];
        if (!isset($seen[$ch])) {
            $seen[$ch] = true;
            $matrix[] = $ch;
        }
    }

    // Tambahkan sisa huruf alfabet (tanpa J)
    for ($i = 0; $i < 26; $i++) {
        $ch = chr($i + ord('A'));
        if ($ch === 'J') continue;
        if (!isset($seen[$ch])) {
            $seen[$ch] = true;
            $matrix[] = $ch;
        }
    }

    // Konversi ke 5x5 grid
    $grid = [];
    for ($i = 0; $i < 5; $i++) {
        $grid[$i] = array_slice($matrix, $i * 5, 5);
    }

    return $grid;
}

function playfair_find_position(array $grid, string $ch): array
{
    $ch = ($ch === 'J') ? 'I' : $ch;
    for ($r = 0; $r < 5; $r++) {
        for ($c = 0; $c < 5; $c++) {
            if ($grid[$r][$c] === $ch) {
                return [$r, $c];
            }
        }
    }
    return [-1, -1];
}

function playfair_prepare_text(string $text): array
{
    $text = strtoupper(preg_replace('/[^a-zA-Z]/', '', $text));
    $text = str_replace('J', 'I', $text);

    $pairs = [];
    $i = 0;
    while ($i < strlen($text)) {
        $a = $text[$i];
        $b = ($i + 1 < strlen($text)) ? $text[$i + 1] : 'X';

        if ($a === $b) {
            $pairs[] = [$a, 'X'];
            $i++;
        } else {
            $pairs[] = [$a, $b];
            $i += 2;
        }
    }

    return $pairs;
}

function playfair_encrypt(string $plaintext, string $key): string
{
    if (empty(preg_replace('/[^a-zA-Z]/', '', $key))) {
        throw new InvalidArgumentException('Key tidak boleh kosong.');
    }

    $grid = playfair_generate_matrix($key);
    $pairs = playfair_prepare_text($plaintext);
    $result = '';

    foreach ($pairs as $pair) {
        [$r1, $c1] = playfair_find_position($grid, $pair[0]);
        [$r2, $c2] = playfair_find_position($grid, $pair[1]);

        if ($r1 === $r2) {
            // Baris sama → geser kanan
            $result .= $grid[$r1][($c1 + 1) % 5];
            $result .= $grid[$r2][($c2 + 1) % 5];
        } elseif ($c1 === $c2) {
            // Kolom sama → geser bawah
            $result .= $grid[($r1 + 1) % 5][$c1];
            $result .= $grid[($r2 + 1) % 5][$c2];
        } else {
            // Persegi → tukar kolom
            $result .= $grid[$r1][$c2];
            $result .= $grid[$r2][$c1];
        }
    }

    return $result;
}

function playfair_decrypt(string $ciphertext, string $key): string
{
    if (empty(preg_replace('/[^a-zA-Z]/', '', $key))) {
        throw new InvalidArgumentException('Key tidak boleh kosong.');
    }

    $grid = playfair_generate_matrix($key);
    $text = strtoupper(preg_replace('/[^a-zA-Z]/', '', $ciphertext));
    $text = str_replace('J', 'I', $text);

    // Pastikan panjang genap
    if (strlen($text) % 2 !== 0) {
        $text .= 'X';
    }

    $result = '';
    for ($i = 0; $i < strlen($text); $i += 2) {
        [$r1, $c1] = playfair_find_position($grid, $text[$i]);
        [$r2, $c2] = playfair_find_position($grid, $text[$i + 1]);

        if ($r1 === $r2) {
            $result .= $grid[$r1][($c1 + 4) % 5];
            $result .= $grid[$r2][($c2 + 4) % 5];
        } elseif ($c1 === $c2) {
            $result .= $grid[($r1 + 4) % 5][$c1];
            $result .= $grid[($r2 + 4) % 5][$c2];
        } else {
            $result .= $grid[$r1][$c2];
            $result .= $grid[$r2][$c1];
        }
    }

    return $result;
}
