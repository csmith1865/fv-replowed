<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    public function index()
    {
        $neighborController = new NeighborController();
        $neighborsData = $neighborController->getNeighborsData();
        
        return view('game', [
            'neighbors' => $neighborsData['neighbors'],
            'neighborIds' => $neighborsData['neighborIds'],
            'neighborsBase64' => $neighborsData['neighborsBase64'],
            'user' => Auth::user()
        ]);
    }
}