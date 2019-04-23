<?php
/**
 * Created by PhpStorm.
 * User: tommy
 * Date: 20-04-2019
 * Time: 16:41
 */

namespace App\traits;


trait Shareable
{
	public function share(){
		var_dump($this->index());
	}
}