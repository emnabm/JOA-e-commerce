<?php

$passphrase = '9bfc6f1b8436725f990ecd3d51e0b1f6ff0a82d82aa7e5512f90234f19163c17';

$config = [
    'private_key_bits' => 4096,
    'private_key_type' => OPENSSL_KEYTYPE_RSA,
    'config' => 'C:/php/extras/ssl/openssl.cnf'
];

$res = openssl_pkey_new($config);

if (!$res) {
    echo 'ERREUR openssl_pkey_new: ' . openssl_error_string() . PHP_EOL;
    exit(1);
}

$exported = openssl_pkey_export($res, $private, $passphrase, $config);

if (!$exported) {
    echo 'ERREUR openssl_pkey_export: ' . openssl_error_string() . PHP_EOL;
    exit(1);
}

file_put_contents('config/jwt/private.pem', $private);

$pub = openssl_pkey_get_details($res);
file_put_contents('config/jwt/public.pem', $pub['key']);

echo 'OK - Cles generees avec succes !' . PHP_EOL;
echo 'private.pem : ' . filesize('config/jwt/private.pem') . ' bytes' . PHP_EOL;
echo 'public.pem  : ' . filesize('config/jwt/public.pem') . ' bytes' . PHP_EOL;