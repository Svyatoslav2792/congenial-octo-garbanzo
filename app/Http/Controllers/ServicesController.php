<?php

namespace App\Http\Controllers;

use App\Service;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    public function execute(){
    if(view()->exists('admin.services')){
        $services=Service::all();
        $data=[
            'title'=>'Сервисы',
            'services'=>$services,

        ];
        return view('admin.services',$data);
    }
    else {
        abort(404);
    }
}
}
