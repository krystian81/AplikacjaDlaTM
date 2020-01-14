<?php
namespace App\CRUD;

//crud/Users.CRUD

use App\Entity\User;
use Doctrine\ORM\EntityManager; 

class UsersCRUD {
	public $entityManager;
	
	public function setEntityManager($entityManager) {
		$this->entityManager = $entityManager;
	}
	
	public function createUser($name, $surname, $login, $password, $admin) {
		$user = new User();
		$user->setName($name);
		$user->setSurname($surname);
		$user->setLogin(strtolower($login));
		$user->setPassword($password);
		$user->setAdmin($admin);
		
        $this->entityManager->persist($user);
        $this->entityManager->flush();
		return $user;
	}
	
	public function getUsers() {
		$users = $this->entityManager->getRepository(User::class)->findAll();
		return $users;
	}

	public function getUser($login, $password) {
		$user = $this->entityManager->getRepository(User::class)->findOneBy([
			'login' => $login,
			'password' => $password
		]);
		return $user;
	}
	
	public function getUserByLogin($login) {
		$user = $this->entityManager->getRepository(User::class)->findOneBy([
			'login' => $login,
		]);
		return $user;
	}

	public function getUserById($id) {
		$user = $this->entityManager->getRepository(User::class)->find($id);
		return $user;
	}

	public function updateUser($user) {
		$this->entityManager->persist($user);
		$this->entityManager->flush();	
	}
	
	public function deleteUser($user) {
		$this->entityManager->remove($user);
		$this->entityManager->flush();	
	}	
}
