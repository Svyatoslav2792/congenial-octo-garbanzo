<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Page;
use App\Service;
use App\Portfolio;
use App\People;
use Illuminate\Support\Facades\DB;
use Mail;


class IndexController extends Controller
{
    public function execute(Request $request){

        if($request->isMethod('post')){

            $messages=[
                'required'=>"Поле :attribute обязательно к заполнению!",
                'email'=>"Введите :attribute действительный электронный адрес!",
            ];

            $this->validate($request,[
                'g-recaptcha-response'=>'required',
            ],$messages);

            //
            //проверка рекапчи
            if( $curl = curl_init() ) {
                $post_data=[
                    'secret'=>'6LdjW84UAAAAADqn7c5p6qlxgF95XwEYo5LT7KcF',
                    'response'=>$request['g-recaptcha-response'],
                ];
                curl_setopt($curl, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
                curl_setopt($curl, CURLINFO_HEADER_OUT, true);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
                $out = curl_exec($curl);
                curl_close($curl);
                if (strpos($out,'true')!=false)
                {
                    $this->validate($request,[
                        'name'=>'required|max:255',
                        'email'=>'required|email',
                        'text'=>'required',
                    ],$messages);
                }

            }

            $data=$request->all();
            Mail::send( 'site.email',['data'=>$data],function ($message) use ($data){
                $message->to('fastfighter92@gmail.com')->subject('Письмо с сайта');
                $message->from('mamkin.raketchik@mail.ru',$data['name']);
            });
            $request->session()->flash('status', 'Письмо отправлено');

            return redirect()->route('home');


        }
        $pages=Page::all();
        $portfolios=Portfolio::all();
        $services=Service::all();
        $peoples=People::all();
        $tags=DB::table('portfolios')->distinct()->pluck('filter');
        //dd($request);
        //dd($pages,$portfolios,$services,$peoples);
        $menu=array();
        foreach($pages as $page){
            $item=array('title'=>$page->name,'alias'=>$page->alias);
            array_push($menu,$item);
        }
        $item=array('title'=>'Services','alias'=>'service');
        array_push($menu,$item);
        $item=array('title'=>'Portfolio','alias'=>'Portfolio');
        array_push($menu,$item);
        $item=array('title'=>'Team','alias'=>'team');
        array_push($menu,$item);
        $item=array('title'=>'Contacts','alias'=>'contact');
        array_push($menu,$item);
        //dd($menu);
        return view('site.index',array(
            'menu'=>$menu,
            'pages'=>$pages,
            'portfolios'=>$portfolios,
            'services'=>$services,
            'peoples'=>$peoples,
            'tags'=>$tags,
        ));
    }

}
