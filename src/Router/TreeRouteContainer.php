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
namespace MinusFour\Router;

use MinusFour\Utils\Tree\Tree;
use MinusFour\Utils\Tree\TreeInterface;
use MinusFour\Utils\Tree\NodeNotFoundException;

class TreeRouteContainer implements RouteContainerInterface {

	private $dataStructure;
	private $routeNameHolder;

	public function __construct(TreeInterface $tree = null) {
		if($tree == null){
			$this->dataStructure = new Tree();
		} else {
			$this->dataStructure = $tree;
		}
	}

	public function addRoute(RouteInterface $route){
		$routeName = $route->getName();
		$this->routeNameHolder[$routeName] = &$route;
		$this->dataStructure->addNode($route->getPath(), $route);
	}

	public function getRouteByName($name){
		if(isset($this->routeNameHolder[$name])){
			return $this->routeNameHolder[$name];
		} else {
			throw new RouteNotFoundException("Route by name '$name' was not found.");
		}
	}

	public function matchRoute($path){
		try {
			$result = $this->dataStructure->traverse($path);
			if($result['cursor']->getObject() == null){
				$routeNotFound = true;
			}
		} catch (NodeNotFoundException $e){
			$routeNotFound = true;
		} finally {
			if(isset($routeNotFound)){
				throw new RouteNotFoundException("Route '$path' was not found.");
			}
		}
		$arr['route'] = $result['cursor']->getObject();
		$arr['parameters'] = $result['parameters'];
		return $arr;
	}
}
?>
