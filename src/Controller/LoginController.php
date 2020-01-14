<?php
// src/Controller/LoginController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\CRUD\UsersCRUD;

class LoginController extends AbstractController
{	
    public function showLoginPage()
    {
        return $this->render('login.html.twig');
    }
	
    public function login(Request $request)
    {
		if($request->request->has('addAcount')) {
			return $this->redirectToRoute('create_account');		
		}
		
        $login = $request->request->get('login');
        $password = $request->request->get('password');
	
		$usersCRUD = new UsersCRUD();
		$usersCRUD->setEntityManager($this->getDoctrine()->getManager());

		$user = $usersCRUD->getUser($login, $password);
		
		if($user != null) {
			$request->getSession()->set("user", $user);
			return $this->redirectToRoute('panel');
		} else {
			return $this->render('login.html.twig', ["error" => "Nieprawidłowe poświadczenie"]);
		}
    }

    public function logout(Request $request)
    {
		$request->getSession()->remove("user");
		return $this->render('login.html.twig');
    }
}
