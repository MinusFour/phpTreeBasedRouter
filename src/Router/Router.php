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

class Router extends RouterAbstract {

	public function dispatch($method, $path){
		$result = $this->routeContainer->matchRouteMethod($method, $path);
		$action = $result['action'];
		$fixedArgs = $action->getFixedArgs();
		$pmerge = $fixedArgs + $result['parameters'];
		$callable = array($action->getClass(), $action->getMethod());
		if(is_callable($callable)){
			call_user_func_array($callable, $pmerge);
		} else {
			throw new \BadMethodCallException;
		}
	}
}

?>
