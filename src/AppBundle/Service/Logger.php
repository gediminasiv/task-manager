<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use AppBundle\Entity\TaskLog;

class Logger {

    protected $em;
    protected $statusBefore;
    protected $statusAfter;
    protected $task;
    protected $userChanged;
    protected $userBefore;
    protected $userAfter;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function logStatus($task, $newStatus, $userChanged, $user)
    {
        $taskLog = new TaskLog();

        $taskLog->setTask($task);
        $taskLog->setStatusBefore($task->getStatus());
        $taskLog->setStatusAfter($newStatus);
        $taskLog->setUserChanged($userChanged);
        $taskLog->setUserBefore($task->getUser());
        $taskLog->setUserAfter($user);
        $taskLog->setCreated(new \DateTime());

        $this->em->persist($taskLog);
        $this->em->flush();

        return $taskLog;
    }

    public function processLog()
    {
        $taskLog = new TaskLog();

        $taskLog->setTask($this->task);
        $taskLog->setStatusBefore($this->statusBefore);
        $taskLog->setStatusAfter($this->statusAfter);
        $taskLog->setUserChanged($this->userChanged);
        $taskLog->setUserBefore($this->userBefore);
        $taskLog->setUserAfter($this->userAfter);
        $taskLog->setCreated(new \DateTime());

        $this->em->persist($taskLog);
        $this->em->flush();

        return $taskLog;
    }

    public function setStatusBefore($statusBefore)
    {
        $this->statusBefore = $statusBefore;
    }

    public function setTask($task)
    {
        $this->task = $task;
    }

    public function setStatusAfter($statusAfter)
    {
        $this->statusAfter = $statusAfter;
    }

    public function setUserChanged($userChanged)
    {
        $this->userChanged = $userChanged;
    }

    public function setUserBefore($userBefore)
    {
        $this->userBefore = $userBefore;
    }

    public function setUserAfter($userAfter)
    {
        $this->userAfter = $userAfter;
    }
}
