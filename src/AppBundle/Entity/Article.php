<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Article
 *
 * @ORM\Table(name="article")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ArticleRepository")
 */
class Article extends BaseEntity
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="publication_date", type="date")
     */
    private $publicationDate;

    /**
     * @var bool
     *
     * @ORM\Column(name="archived", type="boolean")
     */
    private $archived;

    /**
     * article tag
     *@ORM\ManyToOne(targetEntity="AppBundle\Entity\Tag")
     * @ORM\JoinColumn(name="tag_id", referencedColumnName="id", nullable=false)
     */
    private $tag;

    /**
     * related Event
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Event", inversedBy="article")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id" ,nullable=true)
     */
    private $event;

    /**
     * @var array
     * @ORM\Column(name="diapofolder", type="array", nullable=true)
     */
    private $diapofolder;

    /**
     * Article constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->archived= false;
        $this->publicationDate = new \DateTime();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return \DateTime
     */
    public function getPublicationDate()
    {
        return $this->publicationDate;
    }

    /**
     * @param \DateTime $publicationDate
     */
    public function setPublicationDate($publicationDate)
    {
        $this->publicationDate = $publicationDate;
    }

    /**
     * @return bool
     */
    public function isArchived()
    {
        return $this->archived;
    }

    /**
     * @param bool $archived
     */
    public function setArchived($archived)
    {
        $this->archived = $archived;
    }

    /**
     * @return mixed
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param mixed $tag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    }

    /**
     * @return mixed
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param mixed $event
     */
    public function setEvent($event)
    {
        $this->event = $event;
    }

    /**
     * @return array
     */
    public function getDiapofolder()
    {
        return $this->diapofolder;
    }

    /**
     * @param string $diapofolder
     */
    public function setDiapofolder($diapofolder)
    {
        $this->diapofolder = $diapofolder;
    }

}

