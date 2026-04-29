<?php
$ch = curl_init('http://127.0.0.1:8099/admin/test-pdf?version=v3');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 120);
$body = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "HTTP $code\n";
if ($code == 200) {
    file_put_contents('storage/app/public/reports/test-v3-latest.pdf', $body);
    echo "Saved " . strlen($body) . " bytes\n";
} else {
    echo substr($body, 0, 2000) . "\n";
}
curl_close($ch);
