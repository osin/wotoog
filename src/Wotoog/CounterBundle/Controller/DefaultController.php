<?php

namespace Wotoog\CounterBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('WotoogCounterBundle:Default:index.html.twig');
    }

    public function sendMailAction(Request $request){
        $email = $request->request->get('email');
        if(!$email)
            die("oops, l'email n'est pas fourni");
        $body = "$email veut savoir quand le site sera disponible";
        $message = \Swift_Message::newInstance()
            ->setSubject('Feedback demandé')
            ->setFrom($email)
            ->setTo('housseinitoumani@gmail.com')
            ->setBody($body)
        ;
        $this->get('mailer')->send($message);
        return $this->render('WotoogCounterBundle:Default:index.html.twig');
    }
}
