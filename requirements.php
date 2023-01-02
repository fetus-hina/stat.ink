<?php

/**
 * Application requirement checker script.
 *
 * In order to run this script use the following console command:
 * php requirements.php
 *
 * In order to run this script from the web, you should copy it to the web root.
 * If you are using Linux you can create a hard link instead, using the following command:
 * ln requirements.php ../requirements.php
 */

declare(strict_types=1);

// you may need to adjust this path to the correct Yii framework path
$frameworkPath = dirname(__FILE__) . '/vendor/yiisoft/yii2';

if (!is_dir($frameworkPath)) {
    echo '<h1>Error</h1>';
    echo '<p><strong>The path to yii framework seems to be incorrect.</strong></p>';
    echo '<p>You need to install Yii framework via composer or adjust the framework path in file ';
    echo '<abbr title="' . __FILE__ . '">' . basename(__FILE__) . '</abbr>.</p>';
    echo '<p>Please refer to the <abbr title="' . dirname(__FILE__) . '/README.md">README</abbr> ';
    echo 'on how to install Yii.</p>';
}

require_once $frameworkPath . '/requirements/YiiRequirementChecker.php';
$requirementsChecker = new YiiRequirementChecker();

/**
 * Adjust requirements according to your application specifics.
 */
$requirements = array(
    array(
        'name' => 'PDO extension',
        'mandatory' => true,
        'condition' => extension_loaded('pdo'),
        'by' => 'All DB-related classes',
    ),
    array(
        'name' => 'PDO SQLite extension',
        'mandatory' => true,
        'condition' => extension_loaded('pdo_sqlite'),
        'by' => 'All DB-related classes',
        'memo' => 'Required for SQLite database.',
    ),
    array(
        'name' => 'Mcrypt extension',
        'mandatory' => true,
        'condition' => extension_loaded('mcrypt'),
        'by' => 'Favicon generator',
    ),
    array(
        'name' => 'GD extension',
        'mandatory' => true,
        'condition' => extension_loaded('gd'),
        'by' => 'apple-touch-startup-image generator',
    ),
    'phpExposePhp' => array(
        'name' => 'Expose PHP',
        'mandatory' => false,
        'condition' => $requirementsChecker->checkPhpIniOff('expose_php'),
        'by' => 'Security reasons',
        'memo' => '"expose_php" should be disabled at php.ini',
    ),
    'phpAllowUrlInclude' => array(
        'name' => 'PHP allow url include',
        'mandatory' => false,
        'condition' => $requirementsChecker->checkPhpIniOff('allow_url_include'),
        'by' => 'Security reasons',
        'memo' => '"allow_url_include" should be disabled at php.ini',
    ),
);
$requirementsChecker->checkYii()->check($requirements)->render();
