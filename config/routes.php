<?php
use Cake\Routing\Router;

Router::plugin('AclManager', function ($routes) {
    $routes->fallbacks('InflectedRoute');
});
