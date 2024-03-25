<?php

require(__DIR__ . '/../setup.php');

$dbSql = __DIR__ . '/../db/bookstore_db.sql';
$dataSql = __DIR__ . '/../db/bookstore_data.sql';

$mysqlCmd = 'mysql -u ' . $_ENV['DB_USER'] . ' -p' . $_ENV['DB_PASS'] . ' ' . $_ENV['DB_DBNAME'];


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
