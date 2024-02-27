<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use App\Models\Customer;


class UserController extends Controller
{
    public function register()
    {
        return view('user.register');
    }

    public function postSignup(Request $request)
    {
        $this->validate($request, [
            'email' => 'email| required| unique:users',
            'password' => 'required| min:4'
        ]);
        $user = new User([
            'name' => $request->fname . ' ' . $request->lname,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
        $user->save();
        $customer = new Customer;
        $customer->user_id = $user->id;
        $customer->fname = $request->fname;
        $customer->lname = $request->lname;
        $customer->addressline = $request->addressline;
        $customer->phone = $request->phone;
        $customer->zipcode = $request->zipcode;
        $customer->save();
        Auth::login($user);
        return redirect()->route('user.register')->with('success', 'you are registered');
    }

    public function login()
    {
        return view('user.login');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

    public function postSignin(Request $request)
    {
        $this->validate($request, [
            'email' => 'email| required',
            'password' => 'required| min:4'
        ]);

        if (auth()->attempt(array('email' => $request->email, 'password' => $request->password))) {

            return redirect('/');
        } else {
            return redirect()->route('user.login')
                ->with('error', 'Email-Address And Password Are Wrong.');
        }
    }
}
