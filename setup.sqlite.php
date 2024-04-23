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
            'sqlite://.' . __DIR__ . '/db/' . $_ENV['BS_DB_DBNAME'] . '.sqlite'
        ),
        /**
         * Create connection only for Bookstore\Model classes
         */
        'Blrf\\Bookstore\\Model\\*'
    );
