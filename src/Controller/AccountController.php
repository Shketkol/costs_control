<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AccountController extends Controller
{
    /**
     * @Route("/accounts", name="accounts")
     */
    public function index()
    {
    	$user = $this->get('security.token_storage')->getToken()->getUser();

    	dump($user);

        return $this->render('account/index.html.twig');
    }
}
