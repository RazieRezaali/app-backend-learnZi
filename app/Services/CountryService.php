<?php 
namespace App\Services;

use App\Models\Country;

class CountryService{

    public function index()
    {
        return Country::all();
    }

}
