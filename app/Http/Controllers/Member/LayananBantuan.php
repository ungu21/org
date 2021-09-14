<?php

namespace App\Http\Controllers\Member;

use App\AppModel\Bantuan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use Mail;
use App\User;

class LayananBantuan extends Controller
{
    
    public function index()
    {
        $layananbantuan = Bantuan::all()->toArray();
       
        return view('member.layanan-bantuan.index', compact('layananbantuan'));
    }
}