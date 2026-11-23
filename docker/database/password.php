<?php

/**
 * Sets the password of the application's database role from config/db.php.
 *
 * The SCRAM-SHA-256 verifier is computed here so that the plain password is
 * never sent to the server.
 *
 * Usage: php password.php /path/to/config/db.php
 * (run as the "postgres" OS user; connects with peer authentication)
 */

declare(strict_types=1);

$conf = require($argv[1] ?? '/home/statink/stat.ink/config/db.php');
if (!is_array($conf) || !is_string($conf['username'] ?? null) || !is_string($conf['password'] ?? null)) {
    fwrite(STDERR, "Invalid database config\n");
    exit(1);
}

$pdo = new PDO('pgsql:host=/run/postgresql;dbname=postgres', 'postgres', null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);
$pdo->exec(sprintf(
    'ALTER ROLE %s PASSWORD %s',
    quoteIdentifier($conf['username']),
    $pdo->quote(scramSha256Verifier($conf['password'])),
));

/**
 * Builds a verifier in the format PostgreSQL stores in pg_authid.rolpassword.
 *
 * SASLprep is not applied; the generated passwords are plain ASCII, for which
 * it is a no-op.
 */
function scramSha256Verifier(string $password, int $iterations = 4096): string
{
    $salt = random_bytes(16);
    $saltedPassword = hash_pbkdf2('sha256', $password, $salt, $iterations, 32, true);
    $clientKey = hash_hmac('sha256', 'Client Key', $saltedPassword, true);
    $storedKey = hash('sha256', $clientKey, true);
    $serverKey = hash_hmac('sha256', 'Server Key', $saltedPassword, true);

    return sprintf(
        'SCRAM-SHA-256$%d:%s$%s:%s',
        $iterations,
        base64_encode($salt),
        base64_encode($storedKey),
        base64_encode($serverKey),
    );
}

function quoteIdentifier(string $name): string
{
    return '"' . str_replace('"', '""', $name) . '"';
}
