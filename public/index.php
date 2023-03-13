<?php
//TODO:
// - Gestion des erreurs directement dans le service 

declare(strict_types=1);

use Slim\Factory\AppFactory;
use Illuminate\Database\Capsule\Manager as Capsule;

require_once __DIR__ . '/../vendor/autoload.php';
// $settings = require_once __DIR__ . '/settings.php';


$conf = parse_ini_file(__DIR__ . '/../conf/order.db.conf.ini.env');

$capsule = new Capsule;
$capsule->addConnection($conf);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$app = AppFactory::create();
$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(false, false, false);
$errorMiddleware->getDefaultErrorHandler()->forceContentType('application/json'); // Temporary to force JSON on browser

$errorMiddleware->getDefaultErrorHandler()->registerErrorRenderer('application/json', lbs\order\errors\renderer\JsonErrorRenderer::class);


/**
 * configuring API Routes
 */
$app->get('/', lbs\order\actions\HomeAction::class);

$app->get('/orders[/]', lbs\order\actions\OrdersAction::class)->setName('orders');
$app->get('/orders/{id}[/]', lbs\order\actions\OrderByIdAction::class)->setName('ordersById');
$app->get('/orders/{id}/items[/]', lbs\order\actions\OrderItemsAction::class)->setName('ordersItems');
$app->put('/orders/{id}[/]', lbs\order\actions\UpdateOrderAction::class);

$app->run();
