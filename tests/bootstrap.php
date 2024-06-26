<?php

require(__DIR__ . '/../setup.php');

$dbSql = __DIR__ . '/../db/bookstore_db.mysql';
$dataSql = __DIR__ . '/../db/bookstore_data.mysql';

$mysqlCmd = 'mysql -u ' . $_ENV['BS_DB_USER'] . ' -p' . $_ENV['BS_DB_PASS'] . ' ' . $_ENV['BS_DB_DBNAME'];

echo "bootstrap.php: Creating tables ...";
$ret = exec($mysqlCmd . ' < ' . $dbSql . ' 2>&1', $output, $res);
if ($res != 0) {
    throw new \Exception('Failed to create database: ' . $ret);
}
echo "OK.\n";
echo "bootstrap.php: Creating data ...";
$ret = exec($mysqlCmd . ' < ' . $dataSql . ' 2>&1', $output, $res);
if ($res != 0) {
    throw new \Exception('Failed to create data: ' . $ret);
}
echo "OK.\n";
