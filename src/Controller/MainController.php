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
        $number = mt_rand(0, 100);

        return $this->render('main/index.html.twig', compact('number'));
    }
}
