<?php
return [
    'adminEmail' => 'admin@example.com',
    'amazonS3' => require_once(__DIR__ . '/amazon-s3.php'),
    'googleAdsense' => require_once(__DIR__ . '/google-adsense.php'),
    'googleAnalytics' => require_once(__DIR__ . '/google-analytics.php'),
    'googleRecaptcha' => require_once(__DIR__ . '/google-recaptcha.php'),
];
