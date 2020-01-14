<?php
// src/Controller/UsersController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;
use App\CRUD\UsersCRUD;

/**
 * @Route("/api")
 **/
class UsersController extends AbstractController
{	
	public function isUserAuthorized(Request $request) {
		$user = $request->getSession()->get("user");
		if($user != null && $user->getAdmin() == true) {
			return true;
		}	
		return false;
	}

	public function isSessionUserTheSame(Request $request, $id) {
		$user = $request->getSession()->get("user");
		if($user != null && $user->getId() == $id) {
			return true;
		}	
		return false;
	}
	
	/**
	 * @Route("/addUser", name="api_add_user", methods={"PUT"})
	 * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function addUser(Request $request) {
		$data = json_decode(
			$request->getContent(),
			true
		);

		if(!isset($data["login"])) {
			return new JsonResponse(["error" => "Empty login field"], 200);		
		}
		if(!isset($data["password"])) {
			return new JsonResponse(["error" => "Empty password field"], 200);		
		}
		if(!isset($data["name"])) {
			return new JsonResponse(["error" => "Empty name field"], 200);		
		}
		if(!isset($data["surname"])) {
			return new JsonResponse(["error" => "Empty surname field"], 200);		
		}
		if(!isset($data["admin"])) {
			return new JsonResponse(["error" => "Empty admin field"], 200);		
		}

		$validator = Validation::createValidator();

		$constraint = new Assert\Collection(array(
			'login' => new Assert\Length(array('min' => 2)),
			'password' => new Assert\Length(array('min' => 2)),
			'name' => new Assert\Length(array('min' => 2)),
			'surname' => new Assert\Length(array('min' => 2)),
			'admin' => new Assert\Length(array('min' => 1)),
		));
		
		$violations = $validator->validate($data, $constraint);
		
		if($violations->count() > 0) {
			return new JsonResponse(["error" => "Each field require minimum two characters"], 200);
		}
	
		$users = new UsersCRUD();
		$users->setEntityManager($this->getDoctrine()->getManager());
		
		if($users->getUserByLogin($data["login"]) == null) {
			$users->createUser($data["name"], $data["surname"], $data["login"], $data["password"], $data["admin"]);
			return new JsonResponse(["success" => "User " . $data["login"] . " created"], 200);
		} else {
			return new JsonResponse(["error" => "User with the same login already exists"], 200);
		}
	}
	
	/**
	 * @Route("/updateUser", name="api_update_user", methods={"UPDATE"})
	 * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function updateUser(Request $request) {
		$data = json_decode(
			$request->getContent(),
			true
		);
		
		if(!isset($data["id"])) {
			return new JsonResponse(["error" => "Empty id field"], 200);		
		}
		if(!isset($data["name"])) {
			return new JsonResponse(["error" => "Empty name field"], 200);		
		}
		if(!isset($data["surname"])) {
			return new JsonResponse(["error" => "Empty surname field"], 200);		
		}

		$validator = Validation::createValidator();

		$constraint = new Assert\Collection(array(
			'id' => new Assert\Length(array('min' => 1)),
			'name' => new Assert\Length(array('min' => 2)),
			'surname' => new Assert\Length(array('min' => 2)),
		));
		
		$violations = $validator->validate($data, $constraint);
		
		if($violations->count() > 0) {
			return new JsonResponse(["error" => "Each field require minimum two characters"], 200);
		}
		
		if($this->isSessionUserTheSame($request, $data["id"])) {
			$users = new UsersCRUD();
			$users->setEntityManager($this->getDoctrine()->getManager());
			$user = $users->getUserById($data["id"]);

			if($user != null) {
				$user->setName($data["name"]);
				$user->setSurname($data["surname"]);
				$users->updateUser($user);

				return new JsonResponse(["success" => "User " . $user->getLogin() . " updated"], 200);
			} else {
				return new JsonResponse(["error" => "User doesn't exist"], 200);
			}
		} else {
			return new JsonResponse(["error" => "Unauthorized access"], 200);		
		}
	}
	
	/**
	 * @Route("/deleteUser", name="api_remove_user", methods={"DELETE"})
	 * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function deleteUser(Request $request) {
		if($this->isUserAuthorized($request)) {
			$data = json_decode(
				$request->getContent(),
				true
			);
	
			if(!isset($data["id"])) {
				return new JsonResponse(["error" => "Empty id field"], 200);		
			}
			
			$users = new UsersCRUD();
			$users->setEntityManager($this->getDoctrine()->getManager());
			$user = $users->getUserById($data["id"]);
			
			if($user != null) {
				$users->deleteUser($user);
				return new JsonResponse(["success" => "User " . $user->getLogin() . " deleted"], 200);
			} else {
				return new JsonResponse(["error" => "User with id=" . $id . " not exists"], 200);
			}
		} else {
			return new JsonResponse(["error" => "Unauthorized access"], 200);		
		}
	}
	
	/**
	 * @Route("/getUserData", name="api_get_user", methods={"GET"})
	 * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function getUserData(Request $request) {
		if($this->isUserAuthorized($request)) {
			$data = json_decode(
				$request->getContent(),
				true
			);
			
			if(!isset($data["id"])) {
				return new JsonResponse(["error" => "Empty id field"], 200);		
			}
			
			$users = new UsersCRUD();
			$users->setEntityManager($this->getDoctrine()->getManager());
			
			$user = $users->getUserById($data["id"]);
			
			if($user != null) {
				return new JsonResponse(["success" => [
					"id" => $user->getId(), "login" => $user->getLogin, "name" => $user->getName, "surname" => $user->getSurname
				]], 200);
			} else {
				return new JsonResponse(["error" => "User doesn't exist"], 200);
			}
		} else {
			return new JsonResponse(["error" => "Unauthorized access"], 200);		
		}
	}

	/**
	 * @Route("/getUsers", name="api_get_users", methods={"GET"})
	 * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function getUsers(Request $request) {
		if($this->isUserAuthorized($request)) {
			$users = new UsersCRUD();
			$users->setEntityManager($this->getDoctrine()->getManager());
			
			$usersList = $users->getUsers();				
			if($usersList != null) {
				$newUsersList = Array();
				foreach($usersList as $key=>$value) {
					$newUsersList[] = ["id" => $user->getId(), "login" => $user->getLogin, "name" => $user->getName, "surname" => $user->getSurname];
				}
				return new JsonResponse(["success" => $newUsersList], 200);
			} else {
				return new JsonResponse(["error" => "Undefined error"], 200);
			}
		} else {
			return new JsonResponse(["error" => "Unauthorized access"], 200);		
		}
	}


}
