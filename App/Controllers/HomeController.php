<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\HomeModel;
use Core\View;

class HomeController extends Controller
{
    public function renderHome()
    {

        $homeModel = new HomeModel;
        View::renderTwig('Home/home.html');
    }
}
