<?php
require __DIR__ . './vendor/autoload.php';

require './config/config.php';


use Cloudinary\Configuration\Configuration;

Configuration::instance([
  'cloud' => [
    'cloud_name' => $CLOUDINARY_CLOUD_NAME,
    'api_key'    => $CLOUDINARY_API_KEY,
    'api_secret' => $CLOUDINARY_API_SECRET
  ],
  'url' => [
    'secure' => true
  ]
]);
?>