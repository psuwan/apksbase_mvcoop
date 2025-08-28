<?php
namespace App\Modules\Hello;

use App\Core\Controller;

class HelloController extends Controller
{
    public function index(): string
    {
        return $this->view('modules/hello/index', array('title' => 'Hello Module'));
    }
}
