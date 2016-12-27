<?php
$conf = true
    ? require('/home/statink/stat.ink/config/db.php')
    : [ 'username' => 'statink', 'password' => 'hoge' ];
$md5password = 'md5' . hash('md5', $conf['password'] . $conf['username']);

$pdo = new \PDO('pgsql:user=postgres');
$sql = sprintf(
    'ALTER USER %s PASSWORD %s',
    $conf['username'],
    $pdo->quote($md5password)
);
$pdo->exec($sql);
