<?php

/**
 * Setup Dbal and Orm
 */

require(__DIR__ . '/vendor/autoload.php');
require(__DIR__ . '/../dbal/vendor/autoload.php');
require(__DIR__ . '/../orm/vendor/autoload.php');

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
            'mysql://' .
            $_ENV['DB_USER'] . ':' .
            $_ENV['DB_PASS'] . '@' .
            $_ENV['DB_HOST'] . '/' .
            $_ENV['DB_DBNAME']
        )
    );
