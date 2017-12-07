<?php

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Product;

class BlogController extends Controller
{
    /**
     * @Route("/article/{id}", name="Article" , requirements={"id": "\d+"})
     */
    public function productAction($id)
    {
        // replace this example code with whatever you need
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        return $this->render('blog/product.html.twig', ['product' => $product]);
    }

    /**
     * @Route("/articles/{page}", name="Articles", requirements={"page": "\d+"} )
     */
    public function productsAction($page)
    {
      if ($page < 1) {
        throw $this->createNotFoundException("La page ".$page." n'existe pas.");
      }

      $nbPerPage = 3;
      // replace this example code with whatever you need
      $repository = $this->getDoctrine()->getRepository(Product::class);
      $products = $repository->findAllOrderedByDateAdd($page,$nbPerPage);

      // On calcule le nombre total de pages grâce au count($listAdverts) qui retourne le nombre total d'annonces
      $nbPages = ceil(count($products)/$nbPerPage);

      // Si la page n'existe pas, on retourne une 404
      if ($page > $nbPages) {
        throw $this->createNotFoundException("La page ".$page." n'existe pas.");
      }

        $render = [
          'products' => $products,
          'nbPages'  => $nbPages,
          'page'     => $page
          ];

      return $this->render('blog/products.html.twig', $render);
    }
}
