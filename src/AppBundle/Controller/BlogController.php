<?php

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Product;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

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

    /**
     * @Route("/admin/product/add", name="newProduct" )
     */
    public function newProductAction(Request $request)
    {
        // create a product
        $product = new Product();
        $product->setDateAdd(new \DateTime());
        $product->setDateUpd(new \DateTime());

        $form = $this->createFormBuilder($product)
            ->add('productName', TextType::class, array('required' => true))
            ->add('urlAlias', TextType::class)
            ->add('description', TextareaType::class)
            ->add('published', CheckboxType::class, array('label' => 'Publish this product','required' => false))
            ->add('dateAdd', DateType::class)
            ->add('dateUpd', DateType::class)
            ->add('save', SubmitType::class, array('label' => 'Create Product'))
            ->getForm();

        return $this->render('blog/new_product.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
