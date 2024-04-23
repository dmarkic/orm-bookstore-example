<?php

require(__DIR__ . '/../setup.sqlite.php');

$dbSql = __DIR__ . '/../db/bookstore_db.sqlite';
$dataSql = __DIR__ . '/../db/bookstore_data.sqlite';

$sqliteCmd = 'sqlite3 ' . __DIR__ . '/../db/' . $_ENV['BS_DB_DBNAME'] . '.sqlite';


echo "bootstrap.sqlite.php: Creating tables ...";
$ret = exec($sqliteCmd . ' < ' . $dbSql . ' 2>&1', $output, $res);
if ($res != 0) {
    throw new \Exception('Failed to create database: ' . $ret);
}
echo "OK.\n";
echo "bootstrap.sqlite.php: Creating data ...";
$ret = exec($sqliteCmd . ' < ' . $dataSql . ' 2>&1', $output, $res);
if ($res != 0) {
    throw new \Exception('Failed to create data: ' . $ret);
}
echo "OK.\n";
