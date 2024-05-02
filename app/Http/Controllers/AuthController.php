<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function proseslogin(Request $request)
    {
        if (Auth::guard('siswa')->attempt(['nis' => $request->nis, 'password' => $request->password])) {
            return redirect('/dashboard');
        } else {
            return redirect('/')->with(['warning'=>'NIS / Password salah!']);
        }
    }

    public function proseslogout()
    {
        if(Auth::guard('siswa')->check()){
            Auth::guard('siswa')->logout();
            return redirect('/');
        }
    }
}
