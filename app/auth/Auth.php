<?php
/**
 * Created by PhpStorm.
 * User: tommy
 * Date: 29-11-2018
 * Time: 15:49
 */

namespace App\Auth;

use App\Stores\UsersStore;

class Auth
{
	public function __construct($container)
	{
		$this->container = $container;
	}
	public function user(){
		$userStore = new UsersStore($this->container->sqlManager);
		$user = $userStore->byId($_SESSION['user']);
		return $user;
	}

	public function check(){
		return isset($_SESSION['user']);
	}

	public function logout(){
		unset($_SESSION['user']);
	}

	public function attempt($email, $password){
		$userStore = new UsersStore($this->container->sqlManager);
		$user = $userStore->byEmail($email);

		if(!$user){
			return false;
		}

		if(password_verify($password, $user->getPassword())){
			$_SESSION['user'] = $user->getId();
			return true;
		}
		return false;
	}
}