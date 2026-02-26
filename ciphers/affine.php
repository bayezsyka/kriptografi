<?php

/**
 * Affine Cipher
 * E(x) = (a * x + b) mod 26
 * D(x) = a_inv * (x - b) mod 26
 * a harus coprime dengan 26
 */

function gcd(int $a, int $b): int
{
    while ($b !== 0) {
        $temp = $b;
        $b = $a % $b;
        $a = $temp;
    }
    return $a;
}

function mod_inverse(int $a, int $m): int
{
    $a = (($a % $m) + $m) % $m;
    for ($x = 1; $x < $m; $x++) {
        if (($a * $x) % $m === 1) {
            return $x;
        }
    }
    throw new InvalidArgumentException("Modular inverse tidak ditemukan. Nilai a=$a tidak coprime dengan $m.");
}

function affine_encrypt(string $plaintext, int $a, int $b): string
{
    $plaintext = strtoupper(preg_replace('/[^a-zA-Z]/', '', $plaintext));

    if (gcd($a, 26) !== 1) {
        throw new InvalidArgumentException("Nilai 'a' ($a) harus coprime dengan 26. Nilai yang valid: 1,3,5,7,9,11,15,17,19,21,23,25.");
    }

    $result = '';
    for ($i = 0; $i < strlen($plaintext); $i++) {
        $x = ord($plaintext[$i]) - ord('A');
        $c = ($a * $x + $b) % 26;
        $result .= chr($c + ord('A'));
    }

    return $result;
}

function affine_decrypt(string $ciphertext, int $a, int $b): string
{
    $ciphertext = strtoupper(preg_replace('/[^a-zA-Z]/', '', $ciphertext));

    if (gcd($a, 26) !== 1) {
        throw new InvalidArgumentException("Nilai 'a' ($a) harus coprime dengan 26. Nilai yang valid: 1,3,5,7,9,11,15,17,19,21,23,25.");
    }

    $a_inv = mod_inverse($a, 26);
    $result = '';

    for ($i = 0; $i < strlen($ciphertext); $i++) {
        $y = ord($ciphertext[$i]) - ord('A');
        $p = ($a_inv * (($y - $b + 26) % 26)) % 26;
        $p = ($p + 26) % 26;
        $result .= chr($p + ord('A'));
    }

    return $result;
}
