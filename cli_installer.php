<?php

$xcl_url = "xcl.vagrant.dev";

$_SERVER["SERVER_NAME"]    = $xcl_url;
$_SERVER["SCRIPT_NAME"]    = "/v2/index.php";
$_SERVER["REQUEST_METHOD"] = "POST";

$_POST = array(
  "dbHost"              => "localhost",
  "dbUser"              => "root",
  "dbPassword"          => "",
  "dbName"              => "xoops",
  "dbPrefix"            => "xcl4",
  "url"                 => "http://{$xcl_url}/",
  "adminUsername"       => "admin",
  "adminEmail"          => "admin@example.com",
  "adminPassword"       => "admin",
  "adminPasswordVerify" => "admin",
);


// config/config.php 設置
require __DIR__ . "/bootstrap.php";
$controller = new XoopsCube\Installer\Controller\DefaultController($installerConfig, $container);
$controller->run();



// curl 初期化
$cookie = "/tmp/cookie2.txt";
$ch = curl_init();
curl_setopt_array($ch, array(
  CURLOPT_USERAGENT => "xcl cli installer",
  CURLOPT_REFERER => "http://{$xcl_url}/",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_FOLLOWLOCATION => true,
  // Cookie
  CURLOPT_COOKIEJAR => $cookie,
  CURLOPT_COOKIEFILE => $cookie,
  // POST
  CURLOPT_SSL_VERIFYPEER => FALSE,
));



// ログイン
curl_setopt($ch, CURLOPT_URL, "http://{$xcl_url}/user.php");
curl_setopt($ch, CURLOPT_POSTFIELDS, "uname=admin&pass=admin&xoops_login=1");
curl_exec($ch);



// モジュインストール
$data = array(
  "cube_module_install" => 1,
  "uninstalled_modules" => array(
    "legacy",
    "legacyRender",
    "user",
    "profile",
    "altsys",
    "xupdate",
    "stdCache",
  ),
  "option_modules" => array(
    "message",
    "protector",
    "momoImage",
    "mylinks",
  ),
);
curl_setopt($ch, CURLOPT_URL, "http://{$xcl_url}/index.php");
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

$output = curl_exec($ch);

curl_close($ch);

rename(dirname(__DIR__) . "/install", dirname(__DIR__) . "/.install");
