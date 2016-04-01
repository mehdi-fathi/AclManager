<?php
use Cake\Routing\Router;

Router::plugin(
    'AclManager',
    ['path' => '/acl-manager'],
    function ($routes) {
        $routes->fallbacks('DashedRoute');
    }
);
