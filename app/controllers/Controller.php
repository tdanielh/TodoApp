<?php
/**
 * Created by PhpStorm.
 * User: tommy
 * Date: 29-11-2018
 * Time: 11:56
 */

namespace App\Controllers;

class Controller
{
	protected $container;

	public function __construct($container)
	{
		$this->container = $container;
	}

	public function __get($service){
		if($this->container->has($service))
			return $this->container->get($service);
	}
}