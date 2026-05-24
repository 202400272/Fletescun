<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InicioController extends Controller
{
    public function index()
    {
        // Retorna la vista principal del sistema
        return view('index'); 
    }
}