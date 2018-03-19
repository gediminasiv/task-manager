<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class IndexController extends Controller {

    /**
     * @Route("/", name="index")
     */
    public function indexAction(Request $request)
    {
        if (!$this->getUser()) {
            return $this->redirect('login');
        }

        return $this->redirect('dashboard');
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {
        return $this->redirect('index');
    }
}
