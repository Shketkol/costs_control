<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Transaction;
use App\Form\TransactionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class TransactionController
 * @package App\Controller
 *
 * @Route("/transactions")
 */
class TransactionController extends Controller
{
    /**
     * @Route("/", name="transactions")
     */
    public function index(UserInterface $user, Request $request) : Response
    {
        // Get all transactions for this user
        $repository = $this->getDoctrine()
            ->getRepository(Transaction::class);

        $transactionsQuery = $repository
            ->createQueryBuilder('t')
            ->where('t.user = :user')
            ->setParameter('user', $user)
            ->orderBy('t.date', 'DESC')
            ->addOrderBy('t.id', 'DESC')
            ->addOrderBy('t.account', 'ASC')
            ->getQuery()
        ;

        // Add breadcrumbs
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("index_page"));
        $breadcrumbs->addItem("Transactions");

        // Add pagination
        $paginator  = $this->get('knp_paginator');
        $transactions = $paginator->paginate(
            $transactionsQuery,
            $request->query->getInt('page', 1),
            2
        );

//        $transactions->

        return $this->render('transaction/index.html.twig', ['transactions' => $transactions]);
    }

    /**
     * @Route("/new", name="transaction_new")
     */
    public function new(Request $request, UserInterface $user) : Response
    {
        // Create form
        $form = $this->createForm(TransactionType::class);

        // Add breadcrumbs
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("index_page"));
        $breadcrumbs->addItem("Transactions", $this->get("router")->generate("transactions"));
        $breadcrumbs->addItem("New");

        // Handle form submit
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Load data to form
            $transaction = $form->getData();

            // Get account
            $account = $this->getDoctrine()
                ->getRepository(Account::class)
                ->find($transaction->getAccount());

            // Set user
            $transaction->setUser($user);

            // Set account balance after transaction
            $currentBalance = $account->getBalance();

            dump($transaction);

            switch ($transaction->getType()->getCode()) {
                case 'in':
                    $newBalance = $currentBalance + $transaction->getSum();
                    break;

                case 'out':
                    $newBalance = $currentBalance - $transaction->getSum();
                    break;

                default:
                    $newBalance = $currentBalance;
            }

            $account->setBalance($newBalance);
            $transaction->setAccountBalance($newBalance);

            // Save to DB
            $em = $this->getDoctrine()->getManager();
            $em->persist($transaction);

            $em = $this->getDoctrine()->getManager();
            $em->persist($account);

            $em->flush();

            return $this->redirectToRoute('transactions');
        }

        return $this->render('transaction/new.html.twig', ['form' => $form->createView()]);
    }
}
