<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainController extends Controller
{
    /**
    * @Route("/", name="index_page") 
    */
    public function index()
    {
        return $this->redirectToRoute('transactions');
    }
}
