<?php

namespace iahm\ContactBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AppController extends Controller
{
    public function homeAction()
    {
        return $this->render('iahmContactBundle::home.html.twig');
    }
}
