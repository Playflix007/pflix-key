<?php

include "config.php";

$id = $_GET['id'] ?? exit("Error: ID not provided.");
$cacheFile = $cacheFolder . "/$id.es";
$api = "https://babel-in.xyz/babel-b2ef9ad8f0d432962d47009b24dee465/tata/channels/key/$id";
$userAgent = 'Babel-IN'; // u can change if u wanna

// chk if cache exist
if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
    header('Content-Type: application/json');
    readfile($cacheFile);
    exit;
}

$json = fetchMPDManifest($api, $userAgent, $userIP);
$data = json_decode($json, true);
$keyPart = $data['key'];
$keys = json_encode($keyPart, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

header('Content-Type: application/json');
file_put_contents($cacheFile, $keys);
echo $keys;
