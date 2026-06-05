<?php
$ch = curl_init('http://127.0.0.1:5000/auth/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['email' => 'profe_dam@enjoyfe.es', 'password' => '1234']));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$res = curl_exec($ch);
echo "LOGIN RES: " . $res . "\n";
$token = json_decode($res)->access_token ?? '';
curl_close($ch);

$ch2 = curl_init('http://127.0.0.1:5000/academico/dashboard-info');
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token, 'Content-Type: application/json']);
echo curl_exec($ch2);
echo "\nHTTP: " . curl_getinfo($ch2, CURLINFO_HTTP_CODE);
?>
