<?php
return [
    'adminEmail' => 'admin@example.com',
    'amazonS3' => require(__DIR__ . '/amazon-s3.php'),
    'googleAdsense' => require(__DIR__ . '/google-adsense.php'),
    'googleAnalytics' => require(__DIR__ . '/google-analytics.php'),
    'googleRecaptcha' => require(__DIR__ . '/google-recaptcha.php'),
    'lepton' => require(__DIR__ . '/lepton.php'),
    'twitter' => require(__DIR__ . '/twitter.php'),
];
