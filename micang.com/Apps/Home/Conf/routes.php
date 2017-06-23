<?php

return array(
    'URL_ROUTER_ON' => true,
    'URL_ROUTE_RULES' => array(
        '/^news$/' => array('News/index'),
        '/^news\/c([0-9]+)$/' => array('News/lists?cid=:1'),
        '/^news\/([0-9]+)$/' => array('News/detail?id=:1'),
        '/^trolley$/' => array('Trolley/index'),
        '/^shop\/([0-9]+)$/' => array('Shop/detail?id=:1'),
    )
);