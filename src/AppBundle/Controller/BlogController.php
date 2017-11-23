<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class BlogController extends Controller
{
    /**
     * @Route("/article/", name="Article")
     */
    public function productAction()
    {
        // replace this example code with whatever you need
        return $this->render('blog/product.html.twig', []);
    }
}
