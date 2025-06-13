<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class HomeController extends Controller
{
    public function teamSelection()
    {
        return Inertia::render('TeamSelectionView');
    }

    public function fixtureDisplay()
    {
        return Inertia::render('FixtureDisplayView'); 
    }

    public function simulation()
    {
        return Inertia::render('SimulationView');
    }
}