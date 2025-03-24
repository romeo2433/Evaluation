<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DropController extends Controller
{
    public function truncateTable()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;'); // Désactiver les clés étrangères
        DB::table('absences')->truncate();
        DB::table('clients')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;'); // Réactiver les clés étrangères

        
        // Redirection ou message de succès (ajustez si nécessaire)
        return redirect()->back()->with('success', 'Table tronquée avec succès.');
    }
    public function showReinitialisation()
    {
        return view('reinitialisation.index');
 // Assurez-vous d'avoir une vue nommée reinitialisation.blade.php
    }

    // Ajoutez d'autres méthodes ici si nécessaire
}