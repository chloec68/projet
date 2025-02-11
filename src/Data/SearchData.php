<?php 

namespace App\Data;

class SearchData
{
    // public $page = 1;

    public string $q = ''; // champ de l'input type text 

    public array $type = []; //à vérifier

    public array $color= []; //à vérifier

    public int $max;

    public int $min;

}


// cet objet représente les données renvoyées via le formulaire de recherche et les différents champs 