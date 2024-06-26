<?php

/**
 * Setup Dbal and Orm
 */

require('./vendor/autoload.php');

if (file_exists('./setup_local.php')) {
    require('./setup_local.php');
}

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$dotenv->required(['BS_DB_USER', 'BS_DB_PASS', 'BS_DB_HOST', 'BS_DB_DBNAME']);

use Blrf\Bookstore\Log\StderrLogger;
use Blrf\Dbal\Config as DbalConfig;
use Blrf\Orm\Factory as OrmFactory;

$container = OrmFactory::getContainer();
// uncomment to enable logger
//$container->set('blrf.orm.logger', new StderrLogger());

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
        ),
        /**
         * Create connection only for Bookstore\Model classes
         */
        'Blrf\\Bookstore\\Model\\*'
    );
