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

use MinusFour\Router\RouteFactory;
use MinusFour\Router\ActionFactory;
use MinusFour\Router\ActionInterface;
use MinusFour\Router\RouteContainerInterface;
use MinusFour\Utils\JsonFileParser;

class JsonRouteLoader implements RouteLoaderInterface {
	private $filenames;
	private $baseDir;
	private $routeFactory;
	private $actionFactory;

	public function __construct(array $filenames, $baseDir, RouteFactory $routeFactory = null, ActionFactoryInterface $actionFactory = null){
		$this->filenames = $filenames;
		$this->baseDir = $baseDir;
		$this->routeFactory = $routeFactory == null ? new RouteFactory() : $routeFactory;
		$this->actionFactory = $actionFactory == null ? new ActionFactory() : $actionFactory;
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
					//Current Route Path
					$routePath = $this->routeFactory->getCurrentPath();
					//Move to new base point
					$this->routeFactory->addToPath($path);
					$routeLoader = new JsonRouteLoader([$newFile], dirname($fullpath), $this->routeFactory, $this->actionFactory);
					$routeLoader->loadRoutes($routeContainer);
					//Reset route path
					$this->routeFactory->setPath($routePath);
				} else {
					//Debugging purposes:
					//echo "Route: $this->baseRoute" . $path . PHP_EOL;
					//Possibly Normal Route?
					if(isset($route['actions'])){
						//Valid route
						$routeObj = $this->routeFactory->createRoute($routeName, $path);
						$actions = $route['actions'];
						foreach($actions as $method => $action){
							if(!isset($action['fixedArgs'])){
								$actionObj = $this->actionFactory->createAction($action['class'], $action['method']);
							} else {
								$actionObj = $this->actionFactory->createAction($action['class'], $action['method'], $action['fixedArgs']);
							}
							$routeObj->setMethodAction($method, $actionObj);
						}
						$routeContainer->addRoute($routeObj);
					} else {
						//throw new NoActionsInJsonException
					}
				}
			}
		}
	}
}
?>
