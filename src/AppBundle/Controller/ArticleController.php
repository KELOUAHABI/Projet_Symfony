<?php

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Post;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;

class ArticleController extends Controller
{
    /**
     * @Route("/article/{id}", name="Article" , requirements={"id": "\d+"})
     */
    public function showPostAction($id)
    {
        // replace this example code with whatever you need
        $post = $this->getDoctrine()->getRepository(Post::class)->find($id);
        return $this->render('blog/article.html.twig', ['page_name' => 'articles','post' => $post]);
    }

    /**
     * @Route("/articles/{page}", name="Articles", requirements={"page": "\d+"} )
     */
    public function displayPostsAction($page)
    {
      if ($page < 1) {
        throw $this->createNotFoundException("La page ".$page." n'existe pas.");
      }

      $nbPerPage = 3;
      // replace this example code with whatever you need
      $repository = $this->getDoctrine()->getRepository(Post::class);
      $posts = $repository->findAllOrderedByDateAdd($page,$nbPerPage);

      // On calcule le nombre total de pages grâce au count($listAdverts) qui retourne le nombre total d'annonces
      $nbPages = ceil(count($posts)/$nbPerPage);

      // Si la page n'existe pas, on retourne une 404
      if ($page > $nbPages) {
        throw $this->createNotFoundException("La page ".$page." n'existe pas.");
      }

        $render = [
          'page_name' => 'articles',
          'posts'    => $posts,
          'nbPages'  => $nbPages,
          'page'     => $page
          ];

      return $this->render('blog/articles.html.twig', $render);
    }

    /**
     * @Route("/article/add", name="addPost" )
     */
    public function newPostAction(Request $request)
    {
        // create a new post
        $post = new Post();
        $post->setPublished(true);

        $form = $this->createFormBuilder($post)
            ->add('title', TextType::class, array('required' => true))
            ->add('body', CKEditorType::class, array('required' => true))
            ->add('published', CheckboxType::class, array('label' => 'Publier cet article', 'required' => false))
            ->add('save', SubmitType::class, array('label' => 'Créer article'))
            ->getForm();

        // handle the request
        if($request->isMethod('POST') && $form->handleRequest($request)->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $now = new \DateTime();
            $post->setDateAdd($now);
            $post->setDateUpd($now);
            $em->persist($post);
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', 'Article bien enregistrée.');

            // Redirection
            return $this->redirectToRoute('Article', array('id' => $post->getId()));
        }

        return $this->render('blog/form_app.html.twig', array(
            'page_name' => 'articles',
            'libelle' => 'Ajouter un article',
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/article/edit/{id}", name="editPost", requirements={"id": "\d+"})
     */
    public function editPostAction(Request $request, $id)
    {
        // adit a product
        $post = $this->getDoctrine()->getManager()->getRepository(Post::class)->find($id);

        if (!isset($post)) {
          throw $this->createNotFoundException("Cet article n'existe pas.");
        }

        $form = $this->get('form.factory')->createBuilder(FormType::class, $post)
              ->add('title', TextType::class, array('required' => true, 'data' => $post->getTitle()))
              ->add('body', CKEditorType::class, array('required' => true, 'data' => $post->getBody()))
              ->add('published', CheckboxType::class, array('label' => 'Publier cet article', 'required' => false, 'data' => $post->getPublished()))
              ->add('save', SubmitType::class, array('label' => 'Modifier article'))
              ->getForm();

        // handle the request
        if($request->isMethod('POST') && $form->handleRequest($request)->isValid()){
            $em = $this->getDoctrine()->getManager();
            $now = new \DateTime();
            $post->setDateUpd($now);
            $em->persist($post);
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', 'L\'article '.$post->getTitle().' a été modifié avec succès.');

            // On redirige vers la page de visualisation de l'annonce nouvellement créée
            return $this->redirectToRoute('Article', array('id' => $post->getId()));
        }

        return $this->render('blog/form_app.html.twig', array(
            'page_name' => 'articles',
            'libelle' => 'Formulaire de modification de l\'article: '.$post->getTitle().'',
            'form' => $form->createView(),
        ));
    }
}
