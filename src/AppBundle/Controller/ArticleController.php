<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Article controller.
 *
 * @Route("article")
 */
class ArticleController extends Controller
{
    /**
     * Lists all article entities.
     *
     * @Route("/", name="article_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $articles = $em->getRepository('AppBundle:Article')->findBy([],['publicationDate' => 'DESC']);

        return $this->render('article/index.html.twig', array(
            'articles' => $articles,
        ));
    }

    /**
     * Finds and displays a article entity.
     *
     * @Route("/{id}", name="article_show")
     * @Method("GET")
     */
    public function showAction(Request $request, Article $article)
    {
        $url = $request->headers->get('referer');
        if (!$url) {
            $url = $this->generateUrl('homepage');
        }

        return $this->render('article/show.html.twig', array(
            'article' => $article,
            'bachUrl' => $url
        ));
    }

    /**
     * Lists taged articles entities.
     *
     * @Route("/tag/{id}", name="article_taged")
     * @Method("GET"))
     */
    public function tagAction(Tag $tag)
    {
        $em = $this->getDoctrine()->getManager();

        $articles = $em->getRepository('AppBundle:Article')->findBy(['tag'=> $tag],['publicationDate' => 'DESC']);

        return $this->render('article/index.html.twig', array(
            'articles' => $articles,
        ));
    }
}
