<?php
namespace App\Controllers;

use App\Core\Controller;

class SampleController extends Controller
{
    public function index(): string
    {
        return $this->view('sample', array(
            'title' => \App\Core\I18n::t('sample_title')
        ));
    }
}
