<?php

namespace App\Http\Controllers;

use App\Models\Service;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(){
        return view("homepage.home");
    }

    public function viewService($id){
        $data['service'] =Service::findOrFail($id);
        return view("homepage.viewService", $data);
    }

}