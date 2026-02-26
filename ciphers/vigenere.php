<?php

/**
 * Vigenere Cipher - Standard 26 huruf alfabet (A-Z)
 */

function vigenere_encrypt(string $plaintext, string $key): string
{
    $plaintext = strtoupper(preg_replace('/[^a-zA-Z]/', '', $plaintext));
    $key = strtoupper(preg_replace('/[^a-zA-Z]/', '', $key));

    if (empty($key)) {
        throw new InvalidArgumentException('Key tidak boleh kosong.');
    }

    $result = '';
    $keyLen = strlen($key);

    for ($i = 0; $i < strlen($plaintext); $i++) {
        $p = ord($plaintext[$i]) - ord('A');
        $k = ord($key[$i % $keyLen]) - ord('A');
        $c = ($p + $k) % 26;
        $result .= chr($c + ord('A'));
    }

    return $result;
}

function vigenere_decrypt(string $ciphertext, string $key): string
{
    $ciphertext = strtoupper(preg_replace('/[^a-zA-Z]/', '', $ciphertext));
    $key = strtoupper(preg_replace('/[^a-zA-Z]/', '', $key));

    if (empty($key)) {
        throw new InvalidArgumentException('Key tidak boleh kosong.');
    }

    $result = '';
    $keyLen = strlen($key);

    for ($i = 0; $i < strlen($ciphertext); $i++) {
        $c = ord($ciphertext[$i]) - ord('A');
        $k = ord($key[$i % $keyLen]) - ord('A');
        $p = ($c - $k + 26) % 26;
        $result .= chr($p + ord('A'));
    }

    return $result;
}
