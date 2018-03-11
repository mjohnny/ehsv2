<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Archive;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class ArchiveController.
 *
 * @Route("archive")
 * @package AppBundle\Controller
 */
class ArchiveController extends Controller
{

    /**
     * Lists all archive entities.
     *
     * @Route("/", name="archive_index")
     * @Method("GET")
     *
     * @return \Symfony\Component\HttpFoundation\Response
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
     * Display presentation of the association.
     *
     * @Route("/association", name="present_asso")
     * @Method("GET")
     *
     * @return \Symfony\Component\HttpFoundation\Response
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
     *
     * @param \AppBundle\Entity\Archive $archive
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Archive $archive)
    {

        return $this->render('archive/show.html.twig', array(
            'archive' => $archive,
        ));
    }
}
