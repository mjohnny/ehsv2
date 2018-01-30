<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Contact;
use AppBundle\Entity\NewsletterReceiver;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $nextEvent = $this->getDoctrine()->getRepository('AppBundle:Event')->findBy([], ['startDate' => 'DESC'], 1);
        $lastArticles = $this->getDoctrine()->getRepository('AppBundle:Article')->getNoArchivedLastArticles();

        $contact = new Contact();
        $formContact = $this->createForm('AppBundle\Form\ContactType', $contact,
            array('action'=>$this->generateUrl('contact_new')));
//        $formContact->handleRequest($this->get('request_stack')->getCurrentRequest());

        $newsletterReceiver = new NewsletterReceiver();
        $formNewsletter = $this->createForm('AppBundle\Form\NewsletterReceiverType', $newsletterReceiver,
            array('action'=>$this->generateUrl('newsletterreceiver_new')));

        /*******  images  ***********/
        $dir = './thumbs/images/14 janvier 2017';
        $images= [];
        $rdmlist= [];
        // si il contient quelque chose
        if ($dh = opendir($dir)) {
            // boucler tant qu'une image est trouve
            while (($file = readdir($dh)) !== false) {
                if( $file != '.' && $file != '..' && preg_match('#\.(jpe?g|gif|png)$#i', $file)) {
                    $images[]=$file;
                }
            }
            // on ferme la connection
            closedir($dh);
            $nbreMax = (count($images)<8) ? (count($images)) : 8;
            for ($i=0;$i<$nbreMax;$i++){
                $rdm=rand(0,count($images)-1);
                $rdmlist[]=$images[$rdm];
                array_splice($images,$rdm,1);
            }
        }
        /*******  images  ***********/

        return $this->render('default/index.html.twig', [
            'nextEvent' => $nextEvent[0],
            'articles' => $lastArticles,
            'images'=>$rdmlist,
            'formContact' => $formContact->createView(),
            'formNewsletter' => $formNewsletter->createView(),
        ]);
    }

    /**
     * association Infos légales
     *
     * @Route("/infoslegales", name="index_cgu")
     * @Method("GET")
     */
    public function cguAction(){

        return $this->render('default/infosLegales.html.twig');
    }
}
