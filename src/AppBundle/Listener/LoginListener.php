<?php

// Change the namespace according to the location of this class in your bundle
namespace AppBundle\Listener;

use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class LoginListener {

    protected $userManager;
    protected $router;

    public function __construct(UserManagerInterface $userManager, Router $router){
        $this->userManager = $userManager;
        $this->router = $router;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        // $request = $event->getRequest();
        // $session = $request->getSession();
        // $user = $event->getAuthenticationToken()->getUser();
        // $hasSession = is_file(ini_get('session.save_path').'sess_'.$user->getSessionId() );

        // if (true || $user->getLogged() && $hasSession) {
        //     $session->getFlashBag()->add('error', 'Bad shit');

        //     var_dump('test');
        //     die;

        //     return new RedirectResponse($this->router->generate('index'));
        // }
        // if($user->getLogged() && $has_session){
        // throw new AuthenticationException('this user is already logged');
        // }else{
        // $user->setLogged(true);
        // $user->setSessionId($session->getId());
        // $this->userManager->updateUser($user);
        // }
    }
}
