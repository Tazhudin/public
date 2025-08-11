<?php

class Router {
	//масиив маршрутов, таблица маршрутов
	static $routes = [];
	//текущий маршрут вызванный в урл
	static $route = [];

	static function addRoute($regexp, $route = []) {
		//echo "addRoute:<br><<{<br>" . $regexp . "=>" . $route . "<br>}>><br><br>";

		self::$routes[$regexp] = $route;
	}

	static function getRoutes() {
		return self::$routes;
	}

	static function getRoute() {
		return self::$route;
	}

	static function matchRoute($url) {
		foreach (self::$routes as $pattern => $route) {
			if (preg_match("#$pattern#i", $url, $matches)) {
				foreach ($matches as $k => $v) {
					if (is_string($k)) {
						$route[$k] = $v;
					}
				}
				if (!isset($route['action'])) {
					$route['action'] = 'index';
				}
				//debug($matches);
				//debug($route);

				self::$route = $route;
				return true;
			}
		}
		return false;
	}

	static function dispatch($url) {
		if (self::matchRoute($url)) {
			$controller = self::$route['controller'];
			self::upperCamelCase($controller);
			if (class_exists($controller)) {
				echo "class controller exist";
			} else {
				echo "not exist " . $controller . " controller";
			}

			//$action = self::$route['action']);

		} else {
			http_response_code(404);
			include '404.html';
		}
	}

	static function upperCamelCase($name) {
		$name = ucwords($name);
		debug($name);
	}
}
