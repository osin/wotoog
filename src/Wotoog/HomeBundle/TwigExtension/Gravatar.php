<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 27/05/14
 * Time: 11:30
 */

namespace Wotoog\HomeBundle\TwigExtension;

class Gravatar extends \Twig_Extension
{

    // the magic function that makes this easy
    public function getFilters()
    {
        return array(
            'getGravatarImage'    => new \Twig_Filter_Method($this, 'getGravatarImage'),
        );
    }

    public function getGravatarImage($email, $size = 80, $defaultImage = 'mm', $rating = 'G')
    {
        return  $grav_url = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "?d=" . urlencode( $defaultImage ) . "&s=" . $size . '&r=' . $rating;
    }

    // for a service we need a name
    public function getName()
    {
        return 'gravatar';
    }
}