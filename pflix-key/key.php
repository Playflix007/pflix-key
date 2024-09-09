<?php

include "config.php";

$id = $_GET['id'] ?? exit("Error: ID not provided.");
$cacheFile = $cacheFolder . "/$id.es";
$api = "https://babel-in.xyz/babel-b2ef9ad8f0d432962d47009b24dee465/tata/channels/key/$id";
$userAgent = 'Babel-IN'; // आपल्याला हवे असल्यास बदलू शकता

// कॅश फोल्डर चेक करा
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

function fetchMPDManifest($url, $userAgent, $userIP) {
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'User-Agent: ' . $userAgent,
        'X-Forwarded-For: ' . $userIP,
    ]);
    $manifestContent = curl_exec($curl);
    if ($manifestContent === false) {
        $error = curl_error($curl);
        curl_close($curl);
        exit("Error fetching manifest: " . $error);
    }
    curl_close($curl);
    return $manifestContent;
}

function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($ipList[0]);
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6)) {
        return $ip;
    } else {
        return 'Invalid IP address';
    }
}

if ($worldwide === "no") {
    $userIP = getUserIP();
} else {
    $userIP = $serverIP;
}
