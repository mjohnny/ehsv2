<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Archive;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Archive controller.
 *
 * @Route("archive")
 */
class ArchiveController extends Controller
{
    /**
     * Lists all archive entities.
     *
     * @Route("/", name="archive_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $archives = $em->getRepository('AppBundle:Archive')->findAll();

        return $this->render('archive/index.html.twig', array(
            'archives' => $archives,
        ));
    }

    /**
     * @Route("/association", name="present_asso")
     * @Method("GET")
     */
    public function assoAction()
    {
        $presentAsso = $this->getDoctrine()->getRepository('AppBundle:Archive')->findOneBy(['title'=> 'Présentation Association']);
        return $this->render('archive/present_asso.html.twig', ['archive'=>$presentAsso]);
    }

    /**
     * Finds and displays a archive entity.
     *
     * @Route("/{id}", name="archive_show")
     * @Method("GET")
     */
    public function showAction(Archive $archive)
    {

        return $this->render('archive/show.html.twig', array(
            'archive' => $archive,
        ));
    }
}
