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

}
