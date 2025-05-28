<?php 
namespace App\Services;

use Exception;
use App\Models\Country;

class CountryService{

    public function getCountries()
    {
        try{
            return Country::all();
        } 
        catch(Exception $e){
            throw $e;        
        }        
    }

}
