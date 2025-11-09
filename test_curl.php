<?php
$ch = curl_init("https://2ac46e09-f081-4d48-8c00-07c10873a016.azureiotcentral.com");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);

if (curl_errno($ch)) {
    echo '❌ Error: ' . curl_error($ch);
} else {
    echo "✅ Conexión exitosa a Azure IoT Central";
}

curl_close($ch);
