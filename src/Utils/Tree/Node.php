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

class Node implements NodeInterface{
	private $obj;
	private $children;
	private $parameters;

	public function __construct($obj = null){
		$this->obj = $obj;
		$this->children = array();
		$this->parameters = array();
	}

	public function addChild(NodeInterface $node, $id){
		$this->children[$id] = $node;
	}

	public function setObject($obj){
		$this->obj = $obj;
	}
	
	public function getObject() {
		return $this->obj;
	}

	public function getChildren($id){
		if(array_key_exists($id, $this->children)){
			return $this->children[$id];
		} else {
			return null;
		}
	}

	public function addParameterChild(NodeInterface $node, $regex){
		$this->parameters[$regex] = $node;
	}

	public function getMatches($str){
		$arr = array();
		foreach($this->parameters as $regex => $node){
			if(preg_match('/'.$regex.'/', $str)){
				$arr[] = $node;
			}
		}
		return $arr;
	}

	public function getParameter($regex){
		if(array_key_exists($regex, $this->parameters)){
			return $this->parameters[$regex];
		} else {
			return null;
		}
	}

	public function hasObject(){
		return $this->obj != null;
	}

	public function hasChildren(){
		return count($this->children) > 0;
	}

	public function childExists($id){
		return isset($this->children[$id]);
	}

	public function parameterExists($regex){
		return isset($this->parameters[$regex]);
	}

	/* To be deleted 
	 * public function printNode(){
		if($this->hasObject()){
			echo $this->obj->getPath() . PHP_EOL;
		}
		if($this->hasChildren()){
			foreach($this->children as $child){
				$child->printNode();
			}
		}
	 } */

	public function getHash(){
		$path = $this->obj->getPath();
		return substr($path, strrpos($path, '/') + 1);
	}
}

?>
