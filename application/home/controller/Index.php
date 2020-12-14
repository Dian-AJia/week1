<?php
namespace app\home\controller;

class Index
{
    public function index()
    {
        return view('show');
    }

    public function save(){
        $data = input();
        print_r($data);
    }
}
