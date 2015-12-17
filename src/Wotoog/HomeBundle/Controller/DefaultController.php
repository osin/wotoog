<?php

namespace Wotoog\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('WotoogHomeBundle:Default:index.html.twig');
    }
}
