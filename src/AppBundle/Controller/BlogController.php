<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class BlogController extends Controller
{
    /**
     * @Route("/article/{id}", name="Article" , requirements={"id": "\d+"})
     */
    public function productAction($id)
    {
        // replace this example code with whatever you need
        return $this->render('blog/product.html.twig', ['id_product' => $id]);
    }

    /**
     * @Route("/articles/", name="Articles" )
     */
    public function productsAction()
    {
        // replace this example code with whatever you need
        return $this->render('blog/products.html.twig', []);
    }
}
