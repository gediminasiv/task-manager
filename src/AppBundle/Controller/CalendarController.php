<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Task;
use AppBundle\Entity\TaskLog;
use AppBundle\Form\TaskEnterType;
use AppBundle\Form\PasswordChangeType;
use AppBundle\Form\TaskDelayType;

class CalendarController extends Controller {
    /**
     * Lists all archive entities.
     *
     * @Route("/dashboard/calendar", name="calendar_list")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function calendarAction(Request $request)
    {
        return $this->render('calendar/index.html.twig');
    }

    /**
     * Fetches all data for calendar
     *
     * @Route("/calendar/events", name="calendar_events")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function calendarEventsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $from = $request->get('from') ? round($request->get('from')/1000) : null;
        $to = $request->get('to') ? round($request->get('to')/1000) : null;

        $taskRepository = $em->getRepository(Task::class);

        $from = new \DateTime(date('Y-m-d H:i:s', $from));
        $to = new \DateTime(date('Y-m-d H:i:s', $to));

        $tasks = $taskRepository->getTasksByDate($from, $to);

        $result = [];

        $router = $this->get('router');
        $translator = $this->get('translator');

        foreach ($tasks as $key => $task) {
            $result[$key] = [
                'id' => $task->getId(),
                'title' => $task->getDescription(),
                'url' => $router->generate('task_edit', ['id' => $task->getId()]),
            ];

            if ($task->getArchived()) {

            }

            if ($task->getStatus() == 0) { // planned
                $result[$key]['class'] = 'event-success';
                $result[$key]['title'] .= " - ".$translator->trans('planned_at_time');
                $result[$key]['start'] = $task->getPlannedTime() ? $task->getPlannedTime()->getTimestamp()*1000 : 0;
                $result[$key]['finish'] = $task->getPlannedTime() ? $task->getPlannedTime()->getTimestamp()*1000 : 0;
            } else if ($task->getStatus() == 1) { // arrived
                $result[$key]['title'] .= " - ".$translator->trans('arrived_at_task');
                $result[$key]['class'] = 'event-warning';
                $result[$key]['start'] = $task->getArrivalTime() ? $task->getArrivalTime()->getTimestamp()*1000 : 0;
                $result[$key]['finish'] = $task->getArrivalTime() ? $task->getArrivalTime()->getTimestamp()*1000 : 0;
            } else if ($task->getStatus() == 2) {
                $result[$key]['title'] .= " - ".$translator->trans('task_delayed');
                $result[$key]['class'] = 'event-important';
                $result[$key]['start'] = $task->getArrivalTime() ? $task->getArrivalTime()->getTimestamp()*1000 : 0;
                $result[$key]['finish'] = $task->getArrivalTime() ? $task->getArrivalTime()->getTimestamp()*1000 : 0;
            } else if ($task->getStatus() == 3) {
                $result[$key]['title'] .= " - ".$translator->trans('task_finished');
                $result[$key]['class'] = 'event-info';
                $result[$key]['start'] = $task->getDepartureTime() ? $task->getDepartureTime()->getTimestamp()*1000 : 0;
                $result[$key]['finish'] = $task->getDepartureTime() ? $task->getDepartureTime()->getTimestamp()*1000 : 0;
            }
        }

        return $this->json([
            'success' => 1,
            'result' => $result
        ]);
    }
}
