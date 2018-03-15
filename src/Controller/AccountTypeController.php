<?php

namespace App\Controller;

use App\Entity\AccountType;
use App\Form\AccountTypeType as AccountTypeForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/account/types")
 */
class AccountTypeController extends Controller
{
    /**
     * @Route("/", name="account_types")
     */
    public function index(UserInterface $user) : Response
    {
        // Get all types
        $accountTypes = $this->getDoctrine()
            ->getRepository(AccountType::class)
            ->findCommonAndUserTypes($user);

        // Add breadcrumbs
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("index_page"));
        $breadcrumbs->addItem("Accounts", $this->get("router")->generate("accounts"));
        $breadcrumbs->addItem("Types");

        return $this->render('account-type/index.html.twig', ['accountTypes' => $accountTypes]);
    }

    /**
     * @Route("/new", name="account_type_new")
     */
    public function new(Request $request, UserInterface $user) : Response
    {
        $form = $this->createForm(AccountTypeForm::class);

        // Add breadcrumbs
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("index_page"));
        $breadcrumbs->addItem("Accounts", $this->get("router")->generate("accounts"));
        $breadcrumbs->addItem("Types", $this->get("router")->generate("account_types"));
        $breadcrumbs->addItem("New");

        // Handle the submit
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Load data to form
            $accountType = $form->getData();

            // Set user
            $accountType->setUser($user);

            // Save the AccountType
            $em = $this->getDoctrine()->getManager();
            $em->persist($accountType);
            $em->flush();

            return $this->redirectToRoute('account_types');
        }

        return $this->render('account-type/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/delete/{id}", name="account_type_delete")
     * @Method({"GET", "POST", "DELETE"})
     */
    public function delete(AccountType $accountType, UserInterface $user) : Response
    {
        $response = [
            'status' => 'error',
            'message' => null,
            'html'
        ];

        try {
            // Delete account
            $em = $this->getDoctrine()->getManager();
            $em->remove($accountType);
            $em->flush();

            $response['status'] = 'success';
            $response['message'] = 'Account type deleted!';

            // Render index page again
            $accountTypes = $this->getDoctrine()
                ->getRepository(AccountType::class)
                ->findCommonAndUserTypes($user);

            $response['html'] = $this->render('account-type/index.html.twig', ['accountTypes' => $accountTypes]);
        } catch (\Throwable $t) {
            $response['message'] = 'Account type not found!';
        }

        return JsonResponse::create($response);
    }
}
