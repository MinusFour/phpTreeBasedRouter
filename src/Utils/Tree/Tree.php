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
namespace MinusFour\Utils\Tree;

use MinusFour\Utils\PathHelper;

class Tree implements TreeInterface {
	private $root;
	private $nFactory;

	public function __construct(FactoryNodeInterface $factory = null){
		if($factory == null){
			$factory = new FactoryNode();
			$this->nFactory = $factory;
		}
		$this->root = $this->nFactory->createBlank();
	}

	public function addNode($path, $obj){
		if($path == '/') {
			$this->root->setObject($obj);
			return;
		}
		$path = new PathHelper($path);	
		$cursor = $this->root;
		while($path->canPop()){
			$child = $path->popFirstPath();
			$nodeP = $this->isParameter($child);
			if($nodeP){
				$parameter = $this->parseParameter($child);
				$node = $cursor->getParameter($parameter['regEx']);
			} else {
				$node = $cursor->getChildren($child);
			}
			//If node is empty create one.
			if($node != null){
				$cursor = $node;
			} else {
				$tmpNode = $this->nFactory->createBlank();
				if($nodeP){
					$cursor->addParameterChild($tmpNode, $parameter['regEx']);
				} else {
					$cursor->addChild($tmpNode, $child);
				}
				$cursor = $tmpNode;
			}
		}	
		$cursor->setObject($obj);
	}

	public function traverse($path){
		$cpath = new PathHelper($path);	
		$result = $this->nodeTraverse($this->root, $cpath, $cpath->popFirstPath(), array());
		if($result == null) {
			throw new NodeNotFoundException("Node in '$path' not found");
		}
		return $result;
	}

	private function nodeTraverse($cursor, $path, $node, $parameters){
		if($node == null) {
			//End;
			$end['cursor'] = $cursor;
			$end['parameters'] = $parameters;
			return $end;
		} else {
			//Traverse;
			//Next Path, Node
			$nextNode = $path->popFirstPath();
			$child = $cursor->getChildren($node);
			if($child != null){
				//Child was found;
				return $this->nodeTraverse($child, $path, $nextNode, $parameters);
			}
			//Maybe it fits a regular expression?
			$matched = $cursor->getMatches($node);
			if($matched != null){
				$parameters[] = $node;
				foreach($matched as $match){
					//For each matched expression traverse the tree.
					$next = $this->nodeTraverse($match, $path, $nextNode, $parameters);
					if($next != null){
						//If it found a match, then that's it.
						return $next;
					}
				}
			}
			//Nothing found
			return null;
		}
	}

	/* To be deleted
	 * public function printTree(){
		$this->root->printNode();
	} */

	private function isParameter($node){
		return strpos($node, ':') !== false;
	}

	private function parseParameter($node){
		list($arr['name'], $arr['regEx']) = explode(':', $node, 2);
		return $arr;
	}

}
?>
