<?php
// src/Controller/AccountController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\CRUD\UsersCRUD;

class AccountController extends AbstractController
{	
    public function showCreateAccountPage(Request $request)
    {
		return $this->render('createAccount.html.twig');
    }	
}
