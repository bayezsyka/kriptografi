<?php

/**
 * Hill Cipher
 * Enkripsi: C = K * P mod 26
 * Dekripsi: P = K_inv * C mod 26
 * Menggunakan matriks 2x2 atau 3x3
 */

function hill_mod(int $a, int $m): int
{
    return (($a % $m) + $m) % $m;
}

function hill_mod_inverse(int $a, int $m): int
{
    $a = hill_mod($a, $m);
    for ($x = 1; $x < $m; $x++) {
        if (($a * $x) % $m === 1) {
            return $x;
        }
    }
    throw new InvalidArgumentException("Modular inverse tidak ditemukan untuk a=$a mod $m.");
}

function hill_det_2x2(array $matrix): int
{
    return $matrix[0][0] * $matrix[1][1] - $matrix[0][1] * $matrix[1][0];
}

function hill_inverse_2x2(array $matrix): array
{
    $det = hill_mod(hill_det_2x2($matrix), 26);

    if (gcd_hill($det, 26) !== 1) {
        throw new InvalidArgumentException("Matriks tidak memiliki inverse mod 26 (determinan=$det tidak coprime dengan 26).");
    }

    $det_inv = hill_mod_inverse($det, 26);

    return [
        [hill_mod($det_inv * $matrix[1][1], 26), hill_mod($det_inv * (-$matrix[0][1]), 26)],
        [hill_mod($det_inv * (-$matrix[1][0]), 26), hill_mod($det_inv * $matrix[0][0], 26)],
    ];
}

function hill_det_3x3(array $m): int
{
    return $m[0][0] * ($m[1][1] * $m[2][2] - $m[1][2] * $m[2][1])
         - $m[0][1] * ($m[1][0] * $m[2][2] - $m[1][2] * $m[2][0])
         + $m[0][2] * ($m[1][0] * $m[2][1] - $m[1][1] * $m[2][0]);
}

function hill_cofactor_3x3(array $m): array
{
    $cof = [];
    for ($i = 0; $i < 3; $i++) {
        for ($j = 0; $j < 3; $j++) {
            // Minor
            $minor = [];
            for ($r = 0; $r < 3; $r++) {
                if ($r === $i) continue;
                $row = [];
                for ($c = 0; $c < 3; $c++) {
                    if ($c === $j) continue;
                    $row[] = $m[$r][$c];
                }
                $minor[] = $row;
            }
            $det_minor = $minor[0][0] * $minor[1][1] - $minor[0][1] * $minor[1][0];
            $sign = (($i + $j) % 2 === 0) ? 1 : -1;
            $cof[$i][$j] = $sign * $det_minor;
        }
    }
    return $cof;
}

function hill_inverse_3x3(array $matrix): array
{
    $det = hill_mod(hill_det_3x3($matrix), 26);

    if (gcd_hill($det, 26) !== 1) {
        throw new InvalidArgumentException("Matriks tidak memiliki inverse mod 26 (determinan=$det tidak coprime dengan 26).");
    }

    $det_inv = hill_mod_inverse($det, 26);
    $cof = hill_cofactor_3x3($matrix);

    // Transpose cofactor = adjugate
    $adj = [];
    for ($i = 0; $i < 3; $i++) {
        for ($j = 0; $j < 3; $j++) {
            $adj[$i][$j] = hill_mod($det_inv * $cof[$j][$i], 26);
        }
    }

    return $adj;
}

function gcd_hill(int $a, int $b): int
{
    $a = abs($a);
    $b = abs($b);
    while ($b !== 0) {
        $temp = $b;
        $b = $a % $b;
        $a = $temp;
    }
    return $a;
}

/**
 * Parse key string menjadi matriks 2x2 atau 3x3
 * Format input: "a,b,c,d" untuk 2x2 atau "a,b,c,d,e,f,g,h,i" untuk 3x3
 * Bisa juga berupa huruf (4 huruf = 2x2, 9 huruf = 3x3)
 */
function hill_parse_key(string $keyStr): array
{
    // Coba parse sebagai angka (comma-separated)
    if (strpos($keyStr, ',') !== false) {
        $values = array_map('intval', array_map('trim', explode(',', $keyStr)));
        $count = count($values);

        if ($count === 4) {
            return [
                [$values[0], $values[1]],
                [$values[2], $values[3]],
            ];
        } elseif ($count === 9) {
            return [
                [$values[0], $values[1], $values[2]],
                [$values[3], $values[4], $values[5]],
                [$values[6], $values[7], $values[8]],
            ];
        } else {
            throw new InvalidArgumentException("Key harus berisi 4 angka (2x2) atau 9 angka (3x3), dipisahkan koma.");
        }
    }

    // Parse sebagai huruf
    $letters = strtoupper(preg_replace('/[^a-zA-Z]/', '', $keyStr));
    $count = strlen($letters);

    if ($count === 4) {
        return [
            [ord($letters[0]) - ord('A'), ord($letters[1]) - ord('A')],
            [ord($letters[2]) - ord('A'), ord($letters[3]) - ord('A')],
        ];
    } elseif ($count === 9) {
        $matrix = [];
        for ($i = 0; $i < 3; $i++) {
            $row = [];
            for ($j = 0; $j < 3; $j++) {
                $row[] = ord($letters[$i * 3 + $j]) - ord('A');
            }
            $matrix[] = $row;
        }
        return $matrix;
    } else {
        throw new InvalidArgumentException("Key harus berisi 4 atau 9 huruf/angka.");
    }
}

function hill_encrypt(string $plaintext, string $keyStr): string
{
    $matrix = hill_parse_key($keyStr);
    $n = count($matrix);
    $plaintext = strtoupper(preg_replace('/[^a-zA-Z]/', '', $plaintext));

    // Padding dengan X jika perlu
    while (strlen($plaintext) % $n !== 0) {
        $plaintext .= 'X';
    }

    $result = '';
    for ($i = 0; $i < strlen($plaintext); $i += $n) {
        $block = [];
        for ($j = 0; $j < $n; $j++) {
            $block[] = ord($plaintext[$i + $j]) - ord('A');
        }

        for ($r = 0; $r < $n; $r++) {
            $sum = 0;
            for ($c = 0; $c < $n; $c++) {
                $sum += $matrix[$r][$c] * $block[$c];
            }
            $result .= chr(hill_mod($sum, 26) + ord('A'));
        }
    }

    return $result;
}

function hill_decrypt(string $ciphertext, string $keyStr): string
{
    $matrix = hill_parse_key($keyStr);
    $n = count($matrix);

    if ($n === 2) {
        $inv = hill_inverse_2x2($matrix);
    } elseif ($n === 3) {
        $inv = hill_inverse_3x3($matrix);
    } else {
        throw new InvalidArgumentException("Hanya mendukung matriks 2x2 atau 3x3.");
    }

    $ciphertext = strtoupper(preg_replace('/[^a-zA-Z]/', '', $ciphertext));

    if (strlen($ciphertext) % $n !== 0) {
        $ciphertext .= 'X';
    }

    $result = '';
    for ($i = 0; $i < strlen($ciphertext); $i += $n) {
        $block = [];
        for ($j = 0; $j < $n; $j++) {
            $block[] = ord($ciphertext[$i + $j]) - ord('A');
        }

        for ($r = 0; $r < $n; $r++) {
            $sum = 0;
            for ($c = 0; $c < $n; $c++) {
                $sum += $inv[$r][$c] * $block[$c];
            }
            $result .= chr(hill_mod($sum, 26) + ord('A'));
        }
    }

    return $result;
}
