<?php 
namespace App\Services;

use App\Models\Card;

class CardService{

    public function store(array $data): Card
    {
        $card = Card::create([
            'name'     => $data['name']
        ]);
        return $card;
    }

}
