<?php
namespace MinusFour\Router\RouteLoader;

use MinusFour\Router\Route;
use MinusFour\Router\Action;
use MinusFour\Router\RouteContainerInterface;

class JsonRouteLoader {
	protected $routes;
	
	public function __construct($fileName){
		$fileContent = file_get_contents($fileName);
		//Decode Json
		$this->routes = json_decode($fileContent, true);
	}

	public function loadRoutes(RouteContainerInterface $routeContainer){
		foreach($this->routes as $routeName => $route){
			$routeObj = new Route($routeName, $route['path']);
			$actions = $route['actions'];
			foreach($actions as $method => $action){
				if(!isset($action['fixedArgs'])){
					$actionObj = new Action($action['class'], $action['method']);
				} else {
					$actionObj = new Action($action['class'], $action['method'], $action['fixedArgs']);
				}
				$routeObj->setMethodAction($method, $actionObj);
			}
			$routeContainer->addRoute($routeObj);
		}
	}	
}
?>
