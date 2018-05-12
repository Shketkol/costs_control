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
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/accounts")
 */
class AccountController extends Controller
{
    /**
     * Prints list of accounts
     *
     * @Route("/", name="accounts")
     */
    public function index(UserInterface $user) : Response
    {
        // Get all accounts of current user
        $accounts = $this->getDoctrine()
            ->getRepository(Account::class)
            ->findBy(['user' => $user]);

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
    public function new(Request $request, UserInterface $user) : Response
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
            $account->setUser($user);

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
     * @Method({"DELETE"})
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function delete(Account $account, UserInterface $user) : Response
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

            // Get all user accounts
            $accounts = $this->getDoctrine()
                ->getRepository(Account::class)
                ->findBy(['user' => $user]);

            // Get view block (not whole template)
            $template = $this->get('twig')->loadTemplate('account/index.html.twig');
            $tableRows = $template->renderBlock('table_rows', ['accounts' => $accounts]);

            $response['html'] = $tableRows;
        } catch (\Throwable $t) {
            $response['status'] = 'error';
            $response['message'] = 'Account not found!';
        }

        return JsonResponse::create($response);
    }
}
