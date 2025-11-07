<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the home page
     */
    public function index()
    {
        return view('home', [
            'title' => 'Welcome to Laravel',
            'message' => 'This is a PHP application developed with Laravel framework'
        ]);
    }

    /**
     * Display the about page
     */
    public function about()
    {
        return view('about', [
            'title' => 'About Us',
            'description' => 'This is a demo application showcasing Laravel MVC architecture'
        ]);
    }
}
