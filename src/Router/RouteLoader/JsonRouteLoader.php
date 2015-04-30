<?php
/*****************************************************************************/
/* PhpTreeBasedRouter
Copyright (C) 2015 Alejandro Quiroga

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*******************************************************************************/
namespace MinusFour\Router\RouteLoader;

use MinusFour\Router\Route;
use MinusFour\Router\Action;
use MinusFour\Utils\JsonFileParser;
use MinusFour\Router\RouteContainerInterface;
use MinusFour\Router\RouteLoader\RouteLoaderInterface;

class JsonRouteLoader implements RouteLoaderInterface {
	private $filenames;
	private $baseDir;
	private $baseRoute;

	public function __construct(array $filenames, $baseDir, $baseRoute = ''){
		$this->filenames = $filenames;
		$this->baseDir = $baseDir;
		$this->baseRoute = $baseRoute;
	}

	public function loadRoutes(RouteContainerInterface $routeContainer){
		$jsonParser = new JsonFileParser();
		foreach($this->filenames as $filename){
			$routes = $jsonParser->parseFile($this->baseDir . $filename);
			//Debugging purposes:
			//echo "Opening: $this->baseDir" . $filename . PHP_EOL;
			foreach($routes as $routeName => $route){
				$path = $route['path'];
				if(isset($route['include'])){
					//use RouteLoader
					$newFile = $route['include'];
					//Debugging purposes:
					//echo "Attempting to load $newFile" . PHP_EOL;
					$fullpath = realpath($this->baseDir . $newFile);
					$routeLoader = new JsonRouteLoader([$newFile], dirname($fullpath), $path);
					$routeLoader->loadRoutes($routeContainer);
				} else {
					//Debugging purposes:
					//echo "Route: $this->baseRoute" . $path . PHP_EOL;
					//Possibly Normal Route?
					if(isset($route['actions'])){
						//Valid route
						$routeObj = new Route($routeName, $this->baseRoute . $path);
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
					} else {
						//throw new NoActionException
					}
				}
			}
		}		
	}	
}
?>
