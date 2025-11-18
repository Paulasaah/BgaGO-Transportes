<?php

namespace App\Http\Controllers;

use App\Mail\TestMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function index(){

        $data = [
            "title" => "This is testing",
            "order_no" => 123,
            "user" => "Sergio"
        ];

        Mail::to("paulasaavedra1101@gmail.com")->send(new TestMail($data));

        dd("mail sent");
    }
}
