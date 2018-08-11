<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Event
 *
 * @ORM\Table(name="event")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EventRepository")
 */
class Event extends BaseEntity
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
     * @ORM\Column(name="presentation", type="text", nullable=true)
     */
    private $presentation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="date")
     */
    private $startDate;

    /**
     * @var int
     *
     * @ORM\Column(name="place_number", type="integer", nullable=true)
     */
    private $placeNumber;

    /**
     * @var bool
     *
     * @ORM\Column(name="archived", type="boolean")
     */
    private $archived;

    /**
     * event tag
     *@ORM\ManyToOne(targetEntity="AppBundle\Entity\Tag")
     * @ORM\JoinColumn(name="tag_id", referencedColumnName="id")
     */
    private $tag;

    /**
     * inscriptions event
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\EventInscription", mappedBy="event", cascade={"remove"})
     */
    private $inscriptions;

    /**
     * program event
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Program", cascade={"persist","remove" })
     * @ORM\JoinColumn(name="program_id", referencedColumnName="id")
     */
    private $program;

    /**
     * related Article
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Article", mappedBy="event")
     *
     */
    private $article;

    /**
     * Event constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->inscriptions= new ArrayCollection();
        $this->archived = false;
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
    public function getPresentation()
    {
        return $this->presentation;
    }

    /**
     * @param string $presentation
     */
    public function setPresentation($presentation)
    {
        $this->presentation = $presentation;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return int
     */
    public function getPlaceNumber()
    {
        return $this->placeNumber;
    }

    /**
     * @param int $placeNumber
     */
    public function setPlaceNumber($placeNumber)
    {
        $this->placeNumber = $placeNumber;
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
    public function getInscriptions()
    {
        return $this->inscriptions;
    }

    /**
     * @param mixed $inscriptions
     */
    public function setInscriptions($inscriptions)
    {
        $this->inscriptions = $inscriptions;
    }

    /**
     * @return mixed
     */
    public function getProgram()
    {
        return $this->program;
    }

    /**
     * @param mixed $program
     */
    public function setProgram($program)
    {
        $this->program = $program;
    }

    /**
     * @return mixed
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @param mixed $article
     */
    public function setArticle($article)
    {
        $this->article = $article;
    }

    public function __toString()
    {
        return $this->title;
    }
}

