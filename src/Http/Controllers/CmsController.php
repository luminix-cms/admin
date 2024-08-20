<?php

namespace Luminix\Admin\Http\Controllers;

use Illuminate\Routing\Controller;

class CmsController extends Controller
{

    public function render()
    {
        
        return view('admin::cms');
    }

}