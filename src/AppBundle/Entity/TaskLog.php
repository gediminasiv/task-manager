<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TaskLog
 *
 * @ORM\Table(name="task_log")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TaskLogRepository")
 */
class TaskLog
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
     * @ORM\ManyToOne(targetEntity="Task")
     * @ORM\JoinColumn(name="task_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $task;

    /**
     * @var int
     *
     * @ORM\Column(name="status_before", type="integer")
     */
    private $statusBefore;

    /**
     * @var int
     *
     * @ORM\Column(name="status_after", type="integer")
     */
    private $statusAfter;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_changed_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $userChanged;


    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_before_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $userBefore;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_after_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $userAfter;

    /**
     * @var text
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;


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
     * Set statusBefore
     *
     * @param integer $statusBefore
     *
     * @return TaskLog
     */
    public function setStatusBefore($statusBefore)
    {
        $this->statusBefore = $statusBefore;

        return $this;
    }

    /**
     * Get statusBefore
     *
     * @return int
     */
    public function getStatusBefore()
    {
        return $this->statusBefore;
    }

    /**
     * Set statusAfter
     *
     * @param integer $statusAfter
     *
     * @return TaskLog
     */
    public function setStatusAfter($statusAfter)
    {
        $this->statusAfter = $statusAfter;

        return $this;
    }

    /**
     * Get statusAfter
     *
     * @return int
     */
    public function getStatusAfter()
    {
        return $this->statusAfter;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return TaskLog
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
     * Set task
     *
     * @param \AppBundle\Entity\Task $task
     *
     * @return TaskLog
     */
    public function setTask(\AppBundle\Entity\Task $task = null)
    {
        $this->task = $task;

        return $this;
    }

    /**
     * Get task
     *
     * @return \AppBundle\Entity\Task
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * Set userChanged
     *
     * @param \AppBundle\Entity\User $userChanged
     *
     * @return TaskLog
     */
    public function setUserChanged(\AppBundle\Entity\User $userChanged = null)
    {
        $this->userChanged = $userChanged;

        return $this;
    }

    /**
     * Get userChanged
     *
     * @return \AppBundle\Entity\User
     */
    public function getUserChanged()
    {
        return $this->userChanged;
    }

    /**
     * Set userBefore
     *
     * @param \AppBundle\Entity\User $userBefore
     *
     * @return TaskLog
     */
    public function setUserBefore(\AppBundle\Entity\User $userBefore = null)
    {
        $this->userBefore = $userBefore;

        return $this;
    }

    /**
     * Get userBefore
     *
     * @return \AppBundle\Entity\User
     */
    public function getUserBefore()
    {
        return $this->userBefore;
    }

    /**
     * Set userAfter
     *
     * @param \AppBundle\Entity\User $userAfter
     *
     * @return TaskLog
     */
    public function setUserAfter(\AppBundle\Entity\User $userAfter = null)
    {
        $this->userAfter = $userAfter;

        return $this;
    }

    /**
     * Get userAfter
     *
     * @return \AppBundle\Entity\User
     */
    public function getUserAfter()
    {
        return $this->userAfter;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return TaskLog
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }
}
