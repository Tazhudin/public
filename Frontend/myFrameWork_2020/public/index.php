<?php

$query = rtrim($_SERVER['REQUEST_URI'], '/');
//$_SERVER[' QUERY_STRING '];rtrim('posts-new', '/');

require '../vendor/core/Router.php';
require '../vendor/libs/functions.php';
require '../app/controllers/Posts.php';
require '../app/controllers/Main.php';

//Router::addRoute('posts-new', ['controller' => 'posts', 'action' => 'add']);

Router::addRoute('^$', ['controller' => 'main', 'action' => 'index']); //все пустые запросы
Router::addRoute('^(?P<controller>[a-z-]+)/?(?P<action>[a-z-]+)?$');
debug(Router::getRoutes());
//Router::dispatch($query);
//

// if (Router::matchRoute($query)) {
// 	debug(Router::getRoute());
// } else {
// 	echo "<br> 404";
// }