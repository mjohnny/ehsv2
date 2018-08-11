<?php
/**
 * Created by PhpStorm.
 * User: macej
 * Date: 10/08/2018
 * Time: 11:50
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class BaseEntity
 *
 * @package AppBundle\Entity
 */
abstract class BaseEntity
{

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string")
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="alias_path", type="string", unique=true)
     */
    protected $aliasPath;

    /**
     * Created by
     *
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="date")
     */
    protected $createdDate;

    /**
     * BaseEntity constructor.
     */
    public function __construct()
    {
        $this->createdDate = new \DateTime();
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getAliasPath()
    {
        return $this->aliasPath;
    }

    /**
     * @param mixed $aliasPath
     */
    public function setAliasPath($aliasPath)
    {
        $this->aliasPath = $aliasPath;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * @param mixed $createdDate
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;
    }

    /**
     * @param $string
     *
     * @return string
     */
    static function createAliasPath($string)
    {
        $letter = array(
            'a'=>array('À','Á','Â','Ã','Ä','Å','à', 'á', 'â', 'ã', 'ä', 'å'),
            'e'=>array('È','É','Ê','Ë','è', 'é', 'ê', 'ë'),
            'i'=>array('Ì','Í','Î','Ï','ì', 'í', 'î', 'ï'),
            'o'=>array('Ò','Ó','Ô','Õ','Ö','ò', 'ó', 'ô', 'õ', 'ö'),
            'u'=>array('Ù','Ú','Û','Ü','ù', 'ú', 'û', 'ü'),
            'y'=>array('Ý','ý', 'ÿ'));

        $string = strtolower($string); // on met en minuscule
        $string = trim($string); // on enlève les caractères blancs au début et à la fin de la chaine de caractère
        $string = preg_replace('/\s/', '-', $string); // on remplace les caractères blancs par des tirrets
        // on remplace tous les accents
        $string = str_replace($letter['a'], 'a', $string);
        $string = str_replace($letter['e'], 'e', $string);
        $string = str_replace($letter['i'], 'i', $string);
        $string = str_replace($letter['o'], 'o', $string);
        $string = str_replace($letter['u'], 'u', $string);
        $string = str_replace($letter['y'], 'y', $string);
        $string = str_replace(array('ç', 'ñ'), array('c', 'n'), $string);

        $string = preg_replace('/[^a-z0-9-]/', '', $string); // on enlève la ponctuation
        $string = preg_replace('/-{2,}/', '-', $string); // on enlève les tirrets qui sont les uns à coté des autres

        return $string;
    }

}