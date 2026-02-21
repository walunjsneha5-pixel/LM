<?php
// Use environment variables when available (works with AWS Elastic Beanstalk / RDS)
$db_host = getenv('RDS_HOSTNAME') ?: getenv('DB_HOST') ?: 'library-db.cfik8aewcbl1.ap-south-1.rds.amazonaws.com';
$db_user = getenv('RDS_USERNAME') ?: getenv('DB_USER') ?: 'admin';
$db_pass = getenv('RDS_PASSWORD') ?: getenv('DB_PASS') ?: 'Admin098';
$db_name = getenv('RDS_DB_NAME') ?: getenv('DB_NAME') ?: 'library-db';
$db_port = (int)(getenv('RDS_PORT') ?: getenv('DB_PORT') ?: 3306);

// Optional SSL/TLS support for RDS: provide paths via env vars if required
$ssl_ca   = getenv('RDS_SSL_CA') ?: getenv('DB_SSL_CA') ?: '';
$ssl_cert = getenv('RDS_SSL_CERT') ?: getenv('DB_SSL_CERT') ?: '';
$ssl_key  = getenv('RDS_SSL_KEY') ?: getenv('DB_SSL_KEY') ?: '';

$mysqli = mysqli_init();
$flags = 0;
if (($ssl_ca || $ssl_cert || $ssl_key) && function_exists('mysqli_ssl_set')) {
    // Set SSL parameters if any were provided. mysqli_ssl_set must be called
    // before real_connect when using mysqli_init(). Empty values are passed
    // as null to let the underlying library handle them.
    mysqli_ssl_set(
        $mysqli,
        $ssl_key !== '' ? $ssl_key : null,
        $ssl_cert !== '' ? $ssl_cert : null,
        $ssl_ca !== '' ? $ssl_ca : null,
        null,
        null
    );
    $flags |= defined('MYSQLI_CLIENT_SSL') ? MYSQLI_CLIENT_SSL : 0;
}

// Attempt connection (use real_connect so SSL settings are applied when used)
if (!@mysqli_real_connect($mysqli, $db_host, $db_user, $db_pass, $db_name, $db_port, null, $flags)) {
    die("Connection failed: " . mysqli_connect_error());
}

$con = $mysqli;
?>
