<?php
return [
  // 'order.service' => function (\Psr\Container\ContainerInterface $container) {
  //   return new \lbs\order\services\OrderServices($c->get('logger'));
  // },
  'order.service' => function () {
    return new \lbs\order\services\OrderServices();
  },

];
