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
     * @Route("/article/{id}", name="Article" , requirements={"id": "\d+"})
     */
    public function showProductAction($id)
    {
        // replace this example code with whatever you need
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        return $this->render('blog/product.html.twig', ['product' => $product]);
    }

    /**
     * @Route("/articles/{page}", name="Articles", requirements={"page": "\d+"} )
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
          'products' => $products,
          'nbPages'  => $nbPages,
          'page'     => $page
          ];

      return $this->render('blog/products.html.twig', $render);
    }

    /**
     * @Route("/article/add", name="addArticle" )
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

        return $this->render('blog/form_product.html.twig', array(
            'libelle' => 'Formulaire d\'ajout d\'un article',
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/article/edit/{id}", name="editArticle", requirements={"id": "\d+"})
     */
    public function editProductAction(Request $request, $id)
    {
        // adit a product
        $product = $this->getDoctrine()->getManager()->getRepository(Product::class)->find($id);

        if (!isset($product)) {
          throw $this->createNotFoundException("Cet article n'existe pas.");
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

            $request->getSession()->getFlashBag()->add('success', 'L\'artice '.$product->getProductName().' a été modifié avec succès.');

            // On redirige vers la page de visualisation de l'annonce nouvellement créée
            return $this->redirectToRoute('Article', array('id' => $product->getId()));
        }

        return $this->render('blog/form_product.html.twig', array(
            'libelle' => 'Formulaire de modification de l\'article '.$product->getProductName(),
            'form' => $form->createView(),
        ));
    }
}
