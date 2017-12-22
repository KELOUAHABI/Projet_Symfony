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
use Symfony\Component\Form\Extension\Core\Type\FormType;

class BlogController extends Controller
{
    /**
     * @Route("/product/{id}", name="Product" , requirements={"id": "\d+"})
     */
    public function showProductAction($id)
    {
        // replace this example code with whatever you need
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        return $this->render('blog/product.html.twig', ['page_name' => 'products','product' => $product]);
    }

    /**
     * @Route("/products/{page}", name="Products", requirements={"page": "\d+"} )
     */
    public function displayProductsAction($page)
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
          'page_name' => 'products',
          'products' => $products,
          'nbPages'  => $nbPages,
          'page'     => $page
          ];

      return $this->render('blog/products.html.twig', $render);
    }

    /**
     * @Route("/product/add", name="addProduct" )
     */
    public function newProductAction(Request $request)
    {
        // create a new product
        $product = new Product();
        $product->setPublished(true);

        $form = $this->createFormBuilder($product)
            ->add('productName', TextType::class, array('required' => true))
            ->add('urlAlias', TextType::class, array('required' => false))
            ->add('description', TextareaType::class, array('required' => true))
            ->add('published', CheckboxType::class, array('label' => 'Publish this product', 'required' => false))
            ->add('save', SubmitType::class, array('label' => 'Create Product'))
            ->getForm();

        // handle the request
        if($request->isMethod('POST') && $form->handleRequest($request)->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $now = new \DateTime();
            $product->setDateAdd($now);
            $product->setDateUpd($now);
            $em->persist($product);
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', 'Article bien enregistrée.');

            // Redirection
            return $this->redirectToRoute('Article', array('id' => $product->getId()));
        }

        return $this->render('blog/form_app.html.twig', array(
            'libelle' => 'Formulaire d\'ajout d\'un produit',
            'page_name' => 'products',
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/product/edit/{id}", name="editProduct", requirements={"id": "\d+"})
     */
    public function editProductAction(Request $request, $id)
    {
        // adit a product
        $product = $this->getDoctrine()->getManager()->getRepository(Product::class)->find($id);

        if (!isset($product)) {
          throw $this->createNotFoundException("Ce produit n'existe pas.");
        }

        $form = $this->get('form.factory')->createBuilder(FormType::class, $product)
              ->add('productName', TextType::class, array('required' => true, 'data' => $product->getProductName()))
              ->add('urlAlias', TextType::class, array('required' => false, 'data' => $product->getUrlAlias()))
              ->add('description', TextareaType::class, array('required' => true, 'data' => $product->getDescription()))
              ->add('published', CheckboxType::class, array('label' => 'Publish this product','required' => false, 'data' => $product->getPublished()))
              ->add('save', SubmitType::class, array('label' => 'Update Product'))
              ->getForm();

        // handle the request
        if($request->isMethod('POST') && $form->handleRequest($request)->isValid()){
            $em = $this->getDoctrine()->getManager();
            $now = new \DateTime();
            $product->setDateUpd($now);
            $em->persist($product);
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', 'Le produit '.$product->getProductName().' a été modifié avec succès.');

            // On redirige vers la page de visualisation de l'annonce nouvellement créée
            return $this->redirectToRoute('Article', array('id' => $product->getId()));
        }

        return $this->render('blog/form_app.html.twig', array(
            'page_name' => 'products',
            'libelle' => 'Formulaire de modification du produit '.$product->getProductName(),
            'form' => $form->createView(),
        ));
    }
}
