<?php
// src/Controller/LoginController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\CRUD\UsersCRUD;

class PanelController extends AbstractController
{	
    public function showPanelPage(Request $request)
    {
		$user = $request->getSession()->get("user");
		if($user != null) {
			$users = null;
			if($user->getAdmin() == 1) {
				$usersCRUD = new UsersCRUD();
				$usersCRUD->setEntityManager($this->getDoctrine()->getManager());
				$users = $usersCRUD->getUsers();
			}
			return $this->render('panel.html.twig', ["user" => $user, "users" => $users]);
		} else {
			return $this->redirectToRoute('loginForm');		
		}
    }	
}
