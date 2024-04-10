<?php

/**
 * Setup Dbal and Orm
 */

require('./vendor/autoload.php');

/**
 * local file for development or changes.
 */
if (file_exists('./setup_local.php')) {
    require('./setup_local.php');
}

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

print_r($_ENV);

use Blrf\Bookstore\Log\StderrLogger;
use Blrf\Dbal\Config as DbalConfig;
use Blrf\Orm\Factory as OrmFactory;

$container = OrmFactory::getContainer();
// uncomment to enable logger
$container->set('blrf.orm.logger', new StderrLogger());

/**
 * Setup default Orm connection
 */
$manager = OrmFactory::getModelManager();
$manager
    ->addConnection(
        new DbalConfig(
            'mysql://' .
            $_ENV['BS_DB_USER'] . ':' .
            $_ENV['BS_DB_PASS'] . '@' .
            $_ENV['BS_DB_HOST'] . '/' .
            $_ENV['BS_DB_DBNAME']
        )
    );
