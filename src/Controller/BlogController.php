<?php

namespace App\Controller;


use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index (ArticleRepository $repo)
    {

        $articles = $repo->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles
        ]);
    }


    /**
     * @route ("/", name="home")
     */
    public function home()
    {
        return $this->render('blog/home.html.twig');
    }

    /**
     * @route("/blog/new", name ="blog_create")
     * @route("/blog/{id}/edit", name="blog_edit")
     */

    public function form( Request $request, ObjectManager $manager, Article $article = null)
    {
        if (!$article) {
            $article = new Article();
        }

        $article->setTitle("Titre d'exemple")
                ->setContent("le contenu de l'article")
                ->setImage("https://placehold.it/350px150");


        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$article->getId()) {
                $article->setCreatedAt(new \DateTime());
            }

            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('blog/show.html.twig',[
                'id' => $article->getId()]);
        }

        return $this->render('blog/create.html.twig',[
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null
        ]);
    }

    /**
     * @route("/blog/{id}", name="blog_show")
     */

    public function show(Article $article)

    {

        return $this->render('blog/show.html.twig',[
            'article' => $article
        ]);
    }
}




