<?php

namespace App\Controller;

use App\Entity\Account;
use App\Form\AccountType as AccountForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/accounts")
 * @return Response
 */
class AccountController extends Controller
{
    /**
     * @Route("/", name="accounts")
     */
    public function index() : Response
    {
        // Get all accounts
        $accounts = $this->getDoctrine()
            ->getRepository(Account::class)
            ->findAll();

        // Add breadcrumbs
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("index_page"));
        $breadcrumbs->addItem("Accounts");

        return $this->render('account/index.html.twig', ['accounts' => $accounts]);
    }

    /**
     * @Route("/new", name="account_new")
     * @return Response
     */
    public function new(Request $request) : Response
    {
        $form = $this->createForm(AccountForm::class);

        // Add breadcrumbs
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("index_page"));
        $breadcrumbs->addItem("Accounts", $this->get("router")->generate("accounts"));
        $breadcrumbs->addItem("New");

        // Handle the submit
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Load data to form
            $account = $form->getData();

            // Set user
            $currentUser = $this->get('security.token_storage')->getToken()->getUser();
            $account->setUser($currentUser);

            // Save the Account
            $em = $this->getDoctrine()->getManager();
            $em->persist($account);
            $em->flush();

            return $this->redirectToRoute('accounts');
        }

        return $this->render('account/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/delete/{id}", name="account_delete")
     * @Method({"GET", "POST", "DELETE"})
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function delete(Account $account) : Response
    {
        $response = [
            'status' => 'error',
            'message' => null,
            'html'
        ];

        try {
            // Delete account
            $em = $this->getDoctrine()->getManager();
            $em->remove($account);
            $em->flush();

            $response['status'] = 'success';
            $response['message'] = 'Account deleted!';

            // Render index page again
            $accounts = $this->getDoctrine()
                ->getRepository(Account::class)
                ->findAll();

            $response['html'] = $this->render('account/index.html.twig', ['accounts' => $accounts]);
        } catch (\Throwable $t) {
            $this->addFlash('error', 'Account not found!');
            $response['message'] = 'Account not found!';
        }

        return JsonResponse::create($response);
    }
}
