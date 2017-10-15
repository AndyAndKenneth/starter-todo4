<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Views extends Application
{

    public function index()
    {
        $this->data['pagetitle'] = 'Ordered TODO List';
        $tasks = $this->tasks->all();   // get all the tasks
        $this->data['content'] = 'Ok';
        $this->data['leftside'] = $this->makePrioritizedPanel($tasks);
        $this->data['rightside'] = $this->makeCategorizedPanel($tasks);

        $this->render('template_secondary');
    }

    // return -1, 0, or 1 of $a's priority is higher, equal to, or lower than $b's
    function orderByPriority($a, $b)
    {
        if ($a->priority > $b->priority)
            return -1;
        elseif ($a->priority < $b->priority)
            return 1;
        else
            return 0;
    }

    function makePrioritizedPanel($tasks) {
        // extract the undone tasks
        foreach ($tasks as $task)
        {
            if ($task->status != 2)
                $undone[] = $task;
        }

        // Order them by priority
        usort($undone, array("Views", "orderByPriority"));

        // Substitute the priority name
        foreach ($undone as $task) {
            $task->priority = $this->app->priority($task->priority);
        }

        // Convert the array of task objects into an array of associative objects
        foreach ($undone as $task) {
             $converted[] = (array) $task;
        }

        // and then pass them on
        $parms = ['display_tasks' => $converted];
        return $this->parser->parse('by_priority', $parms, true);
    }

    public function makeCategorizedPanel()
    {
         $parms = ['display_tasks' => $this->tasks->getCategorizedTasks()];
         return $this->parser->parse('by_category', $parms, true);
    }

}