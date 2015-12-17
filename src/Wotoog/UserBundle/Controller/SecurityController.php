<?php

namespace Wotoog\UserBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use FOS\UserBundle\Controller\SecurityController as BaseController;

class SecurityController extends BaseController
{
    public function loginAction(\Symfony\Component\HttpFoundation\Request $request){
        $response = parent::loginAction($request);
        return $response;
    }

    protected function renderLogin(array $data)
    {
        $template = sprintf('WotoogUserBundle:Security:login.html.%s', $this->container->getParameter('fos_user.template.engine'));
        return $this->container->get('templating')->renderResponse($template, $data);
    }
}
