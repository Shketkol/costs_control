<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request, AuthenticationUtils $authUtils) : Response
    {
        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        // Create form
        $form = $this->createForm(LoginType::class, [
            '_username' => $lastUsername
        ]);

	    return $this->render('security/login.html.twig', array(
	        'last_username' => $lastUsername,
	        'error'         => $error,
            'form'          => $form->createView(),
	    ));
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction(Request $request, AuthenticationUtils $authUtils)
    {
    	$this->get('security.context')->setToken(null);
        $this->get('request')->getSession()->invalidate();
    }
}
