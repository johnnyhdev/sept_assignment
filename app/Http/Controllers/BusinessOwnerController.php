<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\BusinessOwner;
use App\Http\Controllers\BookingController;

class BusinessOwnerController extends Controller
{
    protected $business;

    public function __construct()
    {
        $this->business = BusinessOwner::first();
    }

    //Returns the guard object for a business owner authentication
    protected static function guard()
    {
        return Auth::guard('web_admin');
    }

    //Returns true if form information is correct to log into business owner account
    public static function login() 
    {
        //Attempt to login as the business owner
        return BusinessOwnerController::guard()->attempt(request(['username', 'password']));
    }

    // Redirects 
    public function index()
    {
        if (BusinessOwner::first() && $this->guard()->check()) 
        {
            return view('admin.index', ['business' => $this->business]);
        }
          //If a business owner exists, but you are not loggined in as the bussiness
        //owner, then redirect to the login page
        elseif (BusinessOwner::first()) 
        {
            return redirect('/login');
        }
        //If a user is logged in, they should not be able to access this page
        elseif (Auth::guard('web_user')->check()) 
        {
            return redirect('/');
        }
        //If no business owner exists, show the business owner registration page
        else 
        {
            return view('admin.register', ['business' => $this->business]);
        }
    }

    public function summary() {
        //If a business owner exists, and you are logged in as the business owner,
        //show the business owner page
        if (BusinessOwner::first() && $this->guard()->check()) 
        {
            return view('admin.summary', ['business' => $this->business]);
        }
        //If a business owner exists, but you are not loggined in as the bussiness
        //owner, then redirect to the login page
        elseif (BusinessOwner::first()) 
        {
            return redirect('/login');
        }
        //If a user is logged in, they should not be able to access this page
        elseif (Auth::guard('web_user')->check()) 
        {
            return redirect('/');
        }
        //If no business owner exists, show the business owner registration page
        else 
        {
            return view('admin.register', ['business' => $this->business]);
        }
    }

    public function employees() 
    {
        //If a business owner exists, and you are logged in as the business owner,
        //show the business owner page
        if (BusinessOwner::first() && $this->guard()->check()) 
        {
            return view('admin.employees', ['business' => $this->business]);
        }
        //If a business owner exists, but you are not loggined in as the bussiness
        //owner, then redirect to the login page
        elseif (BusinessOwner::first()) 
        {
            return redirect('/login');
        }
        //If a user is logged in, they should not be able to access this page
        elseif (Auth::guard('web_user')->check()) 
        {
            return redirect('/');
        }
        //If no business owner exists, show the business owner registration page
        else 
        {
            return redirect('/admin');
        }
    }

    public function history() 
    {
        //If a business owner exists, and you are logged in as the business owner,
        //show the business owner page
        if (BusinessOwner::first() && $this->guard()->check()) 
        {
            // Pass business and booking history data
            return view('admin.history', ['business' => $this->business, 'history' => BookingController::history()]);
        }
        //If a business owner exists, but you are not loggined in as the bussiness
        //owner, then redirect to the login page
        elseif (BusinessOwner::first()) 
        {
            return redirect('/login');
        }
        //If a user is logged in, they should not be able to access this page
        elseif (Auth::guard('web_user')->check()) 
        {
            return redirect('/');
        }
        //If no business owner exists, show the business owner registration page
        else 
        {
            return redirect('/admin');
        }
    }

    public function roster() 
    {
        // Get first record of business owner
        $business = BusinessOwner::first();

        //If a business owner exists, and you are logged in as the business owner,
        //show the business owner page
        if (BusinessOwner::first() && $this->guard()->check()) 
        {
            return view('admin.roster', ['business' => $this->business]);
        }
        //If a business owner exists, but you are not loggined in as the bussiness
        //owner, then redirect to the login page
        elseif (BusinessOwner::first()) 
        {
            return redirect('/login');
        }
        //If a user is logged in, they should not be able to access this page
        elseif (Auth::guard('web_user')->check()) 
        {
            return redirect('/');
        }
        //If no business owner exists, show the business owner registration page
        else 
        {
            return redirect('/admin');
        }
    }

    //Register's a business owner
    public function create()
    {
    	// Validate form
        $this->validate(request(), [
            'businessname' => 'required|max:255|regex:[\w+]',
            'fullname' => 'required|max:255|regex:/^[A-z\-\. ]+$/',
            'username' => 'required|min:6|max:24|alpha_num',
            'password' => 'required|min:6|max:32|confirmed',
            'phone' => 'required|min:10|max:24|regex:/^[0-9\-\+\.\s\(\)x]+$/',
            'address' => 'required|min:6|max:32',
        ]);

    	// Create customer
        $businessOwner = BusinessOwner::create([
            'business_name' => request('businessname'),
            'owner_name' => request('fullname'),
            'username' => request('username'),
            'password' => bcrypt(request('password')),
            'address' => request('address'),
            'phone' => request('phone'),
        ]);

        // Session flash
        session()->flash('message', 'Business Owner registration success');

        //Login as the business owner
        auth()->login($businessOwner);

        //Redirect to the business owner admin page
        return redirect('/admin');
    }
}
