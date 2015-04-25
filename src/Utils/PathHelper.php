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
namespace MinusFour\Utils;

class PathHelper {

	private $path;

	public function __construct($path){
		if($path != '/'){
			$path = rtrim($path, '/');
		}
		$this->path = $path;
	}

	public function popFirstPath(){
		//Three Cases:
		// Root: '/'
		// One Path: '/path'
		// N Paths: '/path/n1/n2'

		//We are at root, so nothing else to pop.
		if(!$this->canPop()){
			return null;
		}

		$secondSlashPos = strpos($this->path, '/', 1);
		if($secondSlashPos === FALSE){
			//We only have one path.
			$firstPath = substr($this->path, 1);
			$remainingPath = '/';
		} else {
			//We have more than one path.
			$firstPath = substr($this->path, 1, $secondSlashPos - 1);
			$remainingPath = substr($this->path, $secondSlashPos);
		}
		$this->path = $remainingPath;
		return $firstPath;
	}

	public function popLastPath(){
		if(!$this->canPop()){
			return null;
		}

		$lastSlash = strrpos($this->path, '/', 1);
		if($lastSlash === FALSE){
			$lastPath = substr($this->path, 1);
			$remainingPath = '/';
		} else {
			$lastPath = substr($this->path, $lastSlash + 1);
			$remainingPath = substr($this->path, 0, $lastSlash);
		}
		$this->path = $remainingPath;
		return $lastPath;
	}

	public function canPop(){
		if($this->path != '/'){
			return true;
		} else {
			return false;
		}
	}

	public function getPath(){
		return $this->path;
	}
}
?>
