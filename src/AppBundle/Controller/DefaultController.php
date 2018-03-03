<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Contact;
use AppBundle\Entity\NewsletterReceiver;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
            array('action' => $this->generateUrl('contact_new')));
//        $formContact->handleRequest($this->get('request_stack')->getCurrentRequest());

        $newsletterReceiver = new NewsletterReceiver();
        $formNewsletter = $this->createForm('AppBundle\Form\NewsletterReceiverType', $newsletterReceiver,
            array('action' => $this->generateUrl('newsletterreceiver_new')));

        /*******  images  ***********/
        $dir = './thumbs/images/14 janvier 2017';
        $images = [];
        $rdmlist = [];
        // si il contient quelque chose
        if ($dh = opendir($dir)) {
            // boucler tant qu'une image est trouve
            while (($file = readdir($dh)) !== false) {
                if ($file != '.' && $file != '..' && preg_match('#\.(jpe?g|gif|png)$#i', $file)) {
                    $images[] = $file;
                }
            }
            // on ferme la connection
            closedir($dh);
            $nbreMax = (count($images) < 8) ? (count($images)) : 8;
            for ($i = 0; $i < $nbreMax; $i++) {
                $rdm = rand(0, count($images) - 1);
                $rdmlist[] = $images[$rdm];
                array_splice($images, $rdm, 1);
            }
        }
        /*******  images  ***********/

        return $this->render('default/index.html.twig', [
            'nextEvent' => $nextEvent[0],
            'articles' => $lastArticles,
            'images' => $rdmlist,
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
    public function cguAction()
    {

        return $this->render('default/infosLegales.html.twig');
    }

    /**
     * Show Gallery
     *
     * @Route("/gallery", name="index_gallery")
     * @Method("GET")
     */
    public function galleryAction()
    {
        $path = './thumbs/gallerie/';
        // recherche des sous dossiers
        $files = [];
        $dirs = ['thumbs' => []];
        if ($dh = opendir($path)) {
            // boucler tant qu'un dossier est trouve
            while (($dir = readdir($dh)) !== false) {
                if ($dir != '.' && $dir != '..' && is_dir($path . $dir)) {
                    $dirs['thumbs'][] = $dir;
                    $diapo = opendir($path . $dir . '/');
                    while (($file = readdir($diapo)) !== FALSE) {
                        if ($file != '.' && $file != '..' && preg_match('#\.(jpe?g|gif|png)$#i', $file)) {
                            $files[$dir] = $file;
                            closedir($diapo);
                            break;
                        }
                    }
                }
            }
            // on ferme la connection
            closedir($dh);
        }

        return $this->render('default/gallery.html.twig', array(
            'fichiers' => $files
        ));
    }

    /**
     * New diapo.
     *
     * @Route("/gallerynew", name="gallery_new")
     * @Method({"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function galleryNewAction()
    {
        if (isset($_POST['diapo']) && $_POST['diapo'] && $_POST['diapo'] !== '0') {
            $newrepo = $_POST['diapo'];
            $sourcepath = './uploads/images/' . $newrepo;
            $targetpath = './thumbs/gallerie/' . $newrepo;
            mkdir($targetpath, 0755, TRUE);
            if ($dh = opendir($sourcepath)) {
                while (($file = readdir($dh)) !== FALSE) {
                    if ($file != '.' && $file != '..' && is_file($sourcepath . '/' . $file))
                        $this->darkroom($sourcepath . '/' . $file, $targetpath . '/' . $file, 0, 1050);
                }
                closedir($dh);
            }
            $this->addFlash('success', $this->get('translator')->trans('diapo.created'));
        }

        $path = './thumbs/gallerie/';
        // recherche des sous dossiers
        $dirs = ['thumbs' => []];
        if ($dh = opendir($path)) {
            // boucler tant qu'un dossier est trouve
            while (($dir = readdir($dh)) !== false) {
                if ($dir != '.' && $dir != '..' && is_dir($path . $dir)) {
                    $dirs['thumbs'][] = $dir;
                }
            }
        }
        // on ferme la connection
        closedir($dh);

        $pathimg = './uploads/images/';
        if ($dhimg = opendir($pathimg)) {
            while (($dirimg = readdir($dhimg)) !== FALSE) {
                if ($dirimg != '.' && $dirimg != '..' && is_dir($pathimg . $dirimg) && !in_array($dirimg, $dirs['thumbs'])) {
                    $dirs['forcreate'][] = $dirimg;
                }
            }
            closedir($dhimg);
        }

        return $this->render('easy_admin/gallery/newGallery.html.twig', array(
            'dossiers' => $dirs
        ));
    }

    /**
     * Show diapo
     *
     * @Route("/gallery/{diapo}", name="diapo_gallery")
     * @Method("GET")
     */
    public
    function diapoAction($diapo)
    {
        return $this->render('default/diapo.html.twig', array(
            'dir' => $diapo
        ));
    }

    /**
     * @param $diapo
     * @Route("/gallery/{diapo}/show", name="show_diapo")
     * @Method("GET")
     *
     */
    public
    function showdiapoAction($diapo)
    {
        # Nom du dossier images à renseigner
        $path = './thumbs/gallerie/' . $diapo . '/';
        // si il contient quelque chose
        $images = [];
        if ($dh = opendir($path)) {
            // boucler tant qu'une image est trouve
            while (($file = readdir($dh)) !== false) {
                if ($file != '.' && $file != '..' && preg_match('#\.(jpe?g|gif|png)$#i', $file)) {
                    $images[] = $file;
                }
            }
            // on ferme la connection
            closedir($dh);
        }
        return $this->render('default/showdiapo.html.twig', array(
            'images' => $images,
            'dir' => $diapo
        ));
    }

    private function darkroom($img, $to, $width = 0, $height = 0, $useGD = TRUE)
    {
        $diapoWidth = 1680;
        $dimensions = getimagesize($img);
        $ratio = $dimensions[0] / $dimensions[1];
        // Calcul des dimensions si 0 passé en paramètre
        if ($width == 0 && $height == 0) {
            $width = $dimensions[0];
            $height = $dimensions[1];
        } elseif ($height == 0) {
            $height = round($width / $ratio);
        } elseif ($width == 0) {
            $width = round($height * $ratio);
        }
        if ($dimensions[0] > ($width / $height) * $dimensions[1]) {
            $dimY = $height;
            $dimX = round($height * $dimensions[0] / $dimensions[1]);
            $decalX = ($dimX - $width) / 2;
            $decalY = 0;
        }
        if ($dimensions[0] < ($width / $height) * $dimensions[1]) {
            $dimX = $width;
            $dimY = round($width * $dimensions[1] / $dimensions[0]);
            $decalY = ($dimY - $height) / 2;
            $decalX = 0;
        }
        if ($dimensions[0] == ($width / $height) * $dimensions[1]) {
            $dimX = $width;
            $dimY = $height;
            $decalX = 0;
            $decalY = 0;
        }
        // Création de l'image avec la librairie GD
        if ($useGD) {
            $pattern = imagecreatetruecolor($diapoWidth, $height);
            $background_color = imagecolorallocate($pattern, 30, 40, 50);
            imagefill($pattern, 0, 0, $background_color); //0,0 représentant le point de départ du remplissage : origine de l'image : en haut à gauche.

            $type = mime_content_type($img);
            switch (substr($type, 6)) {
                case 'jpeg':
                    $image = imagecreatefromjpeg($img);
                    break;
                case 'gif':
                    $image = imagecreatefromgif($img);
                    break;
                case 'png':
                    $image = imagecreatefrompng($img);
                    break;
            }

            if ($width < $diapoWidth) {
                $decal = ($diapoWidth - $width) / 2;
            } else {
                $decal = 0;
            }
            imagecopyresampled($pattern, $image, $decal, 0, 0, 0, $dimX, $dimY, $dimensions[0], $dimensions[1]);
            imagedestroy($image);
            imagejpeg($pattern, $to, 100);
            return TRUE;
        }
        return TRUE;
    }
}
