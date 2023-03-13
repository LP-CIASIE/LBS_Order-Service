<?php

namespace lbs\order\actions;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use lbs\order\errors\exceptions\RessourceNotFoundException;
use Slim\Exception\HttpNotFoundException;

use lbs\order\services\OrderServices;
use lbs\order\services\utils\FormatterAPI;

final class OrdersByClientAction
{
    public function __invoke(Request $rq, Response $rs): Response{
        $query = $rq->getQueryParams();
        $os = new OrderServices();
        try{
            if(isset($query['c'])){
                $orders = $os->getOrdersByClient($query['c']);
            }
        } catch(RessourceNotFoundException $e){
            throw new HttpNotFoundException($rq, $e->getMessage());
        }

        $data = [
            'type' => 'collection',
            'count' => count($orders),
            'orders' => $orders
          ];
      
        return FormatterAPI::formatResponse($rq, $rs, $data);
    }
}