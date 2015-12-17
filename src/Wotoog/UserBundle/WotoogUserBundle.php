<?php

namespace Wotoog\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class WotoogUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
