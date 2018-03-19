<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Task
 *
 * @ORM\Table(name="task")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TaskRepository")
 * @Vich\Uploadable
 */
class Task
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
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var array
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @var float
     *
     * @ORM\Column(name="distance", type="float", length=255, nullable=true)
     */
    private $distance;

    /**
     * @var array
     *
     * @ORM\ManyToOne(targetEntity="Client")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    private $client;

    /**
     * @var array
     *
     * @ORM\ManyToOne(targetEntity="Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    private $project;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="arrival_time", type="datetime", nullable=true)
     */
    private $arrivalTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="departure_time", type="datetime", nullable=true)
     */
    private $departureTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="planned_time", type="datetime", nullable=true)
     */
    private $plannedTime;

    /**
     * @var string
     *
     * @ORM\Column(name="cancelled_comment", type="text", nullable=true)
     */
    private $cancelledComment;

    /**
     * @var string
     *
     * @ORM\Column(name="finished_comment", type="text", nullable=true)
     */
    private $finishedComment;

    /**
     * @var string
     *
     * @ORM\Column(name="receipt_no", type="text", nullable=true)
     */
    private $receiptNo;

    /**
     * @var string
     *
     * @ORM\Column(name="invoice_no", type="text", nullable=true)
     */
    private $invoiceNo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="archived", type="boolean")
     */
    private $archived;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="task_image", fileNameProperty="taskImageName", size="taskImageSize")
     *
     * @var File
     */
    private $imageFile;

    /**
     * @var File
     *
     * @ORM\ManyToOne(targetEntity="File")
     * @ORM\JoinColumn(name="task_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    private $files;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $taskImageName;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var integer
     */
    private $taskImageSize;

    public function __construct()
    {
        $this->created = new \DateTime();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Task
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Task
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Task
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set arrivalTime
     *
     * @param \DateTime $arrivalTime
     *
     * @return Task
     */
    public function setArrivalTime($arrivalTime)
    {
        $this->arrivalTime = $arrivalTime;

        return $this;
    }

    /**
     * Get arrivalTime
     *
     * @return \DateTime
     */
    public function getArrivalTime()
    {
        return $this->arrivalTime;
    }

    /**
     * Set departureTime
     *
     * @param \DateTime $departureTime
     *
     * @return Task
     */
    public function setDepartureTime($departureTime)
    {
        $this->departureTime = $departureTime;

        return $this;
    }

    /**
     * Get departureTime
     *
     * @return \DateTime
     */
    public function getDepartureTime()
    {
        return $this->departureTime;
    }

    /**
     * Set cancelledComment
     *
     * @param string $cancelledComment
     *
     * @return Task
     */
    public function setCancelledComment($cancelledComment)
    {
        $this->cancelledComment = $cancelledComment;

        return $this;
    }

    /**
     * Get cancelledComment
     *
     * @return string
     */
    public function getCancelledComment()
    {
        return $this->cancelledComment;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Task
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set distance
     *
     * @param float $distance
     *
     * @return Task
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * Get distance
     *
     * @return float
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * Set photo
     *
     * @param string $photo
     *
     * @return Task
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return string
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Set invoiceNo
     *
     * @param string $invoiceNo
     *
     * @return Task
     */
    public function setInvoiceNo($invoiceNo)
    {
        $this->invoiceNo = $invoiceNo;

        return $this;
    }

    /**
     * Get invoiceNo
     *
     * @return string
     */
    public function getInvoiceNo()
    {
        return $this->invoiceNo;
    }

    /**
     * Set archived
     *
     * @param boolean $archived
     *
     * @return Task
     */
    public function setArchived($archived)
    {
        $this->archived = $archived;

        return $this;
    }

    /**
     * Get archived
     *
     * @return boolean
     */
    public function getArchived()
    {
        return $this->archived;
    }

    /**
     * Set imageName
     *
     * @param string $imageName
     *
     * @return Task
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;

        return $this;
    }

    /**
     * Get imageName
     *
     * @return string
     */
    public function getImageName()
    {
        return $this->imageName;
    }

    /**
     * Set imageSize
     *
     * @param integer $imageSize
     *
     * @return Task
     */
    public function setImageSize($imageSize)
    {
        $this->imageSize = $imageSize;

        return $this;
    }

    /**
     * Get imageSize
     *
     * @return integer
     */
    public function getImageSize()
    {
        return $this->imageSize;
    }

    /**
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     *
     * @return Product
     */
    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        if ($image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    /**
     * @return File|null
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * Set taskImageName
     *
     * @param string $taskImageName
     *
     * @return Task
     */
    public function setTaskImageName($taskImageName)
    {
        $this->taskImageName = $taskImageName;

        return $this;
    }

    /**
     * Get taskImageName
     *
     * @return string
     */
    public function getTaskImageName()
    {
        return $this->taskImageName;
    }

    /**
     * Set taskImageSize
     *
     * @param integer $taskImageSize
     *
     * @return Task
     */
    public function setTaskImageSize($taskImageSize)
    {
        $this->taskImageSize = $taskImageSize;

        return $this;
    }

    /**
     * Get taskImageSize
     *
     * @return integer
     */
    public function getTaskImageSize()
    {
        return $this->taskImageSize;
    }

    /**
     * Set finishedComment
     *
     * @param string $finishedComment
     *
     * @return Task
     */
    public function setFinishedComment($finishedComment)
    {
        $this->finishedComment = $finishedComment;

        return $this;
    }

    /**
     * Get finishedComment
     *
     * @return string
     */
    public function getFinishedComment()
    {
        return $this->finishedComment;
    }

    /**
     * Set receiptNo
     *
     * @param string $receiptNo
     *
     * @return Task
     */
    public function setReceiptNo($receiptNo)
    {
        $this->receiptNo = $receiptNo;

        return $this;
    }

    /**
     * Get receiptNo
     *
     * @return string
     */
    public function getReceiptNo()
    {
        return $this->receiptNo;
    }

    /**
     * Set plannedTime
     *
     * @param \DateTime $plannedTime
     *
     * @return Task
     */
    public function setPlannedTime($plannedTime)
    {
        $this->plannedTime = $plannedTime;

        return $this;
    }

    /**
     * Get plannedTime
     *
     * @return \DateTime
     */
    public function getPlannedTime()
    {
        return $this->plannedTime;
    }

    /**
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return Task
     */
    public function setClient(\AppBundle\Entity\Client $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \AppBundle\Entity\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set project
     *
     * @param \AppBundle\Entity\Project $project
     *
     * @return Task
     */
    public function setProject(\AppBundle\Entity\Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \AppBundle\Entity\Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set paths
     *
     * @param array $paths
     *
     * @return Task
     */
    public function setPaths($paths)
    {
        $this->paths = $paths;

        return $this;
    }

    /**
     * Get paths
     *
     * @return array
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * Set files
     *
     * @param \AppBundle\Entity\File $files
     *
     * @return Task
     */
    public function setFiles(\AppBundle\Entity\File $files = null)
    {
        $this->files = $files;

        return $this;
    }

    /**
     * Get files
     *
     * @return \AppBundle\Entity\File
     */
    public function getFiles()
    {
        return $this->files;
    }

    public function getTimespent()
    {
        return $this->timespent;
    }
}
