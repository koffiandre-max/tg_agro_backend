<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Produits agricoles surveillés (Côte d'Ivoire)
    |--------------------------------------------------------------------------
    |
    | Liste des produits dont on veut suivre le prix via FAOSTAT.
    | item_code : code élément FAO du produit.
    | element_code : code élément FAO du prix (5532 = Producer Price, USD/t).
    | dataset : jeu de données FAOSTAT (PD_PRICES = Producer Prices).
    */

    'products' => [
        ['name' => 'Cacao', 'item_code' => 661, 'element_code' => 5532, 'unit' => 't', 'dataset' => 'PD_PRICES'],
        ['name' => 'Café', 'item_code' => 656, 'element_code' => 5532, 'unit' => 't', 'dataset' => 'PD_PRICES'],
        ['name' => 'Banane plantain', 'item_code' => 489, 'element_code' => 5532, 'unit' => 't', 'dataset' => 'PD_PRICES'],
        ['name' => 'Manioc', 'item_code' => 125, 'element_code' => 5532, 'unit' => 't', 'dataset' => 'PD_PRICES'],
        ['name' => 'Igname', 'item_code' => 623, 'element_code' => 5532, 'unit' => 't', 'dataset' => 'PD_PRICES'],
        ['name' => 'Riz', 'item_code' => 27, 'element_code' => 5532, 'unit' => 't', 'dataset' => 'PD_PRICES'],
        ['name' => 'Maïs', 'item_code' => 56, 'element_code' => 5532, 'unit' => 't', 'dataset' => 'PD_PRICES'],
        ['name' => 'Ananas', 'item_code' => 687, 'element_code' => 5532, 'unit' => 't', 'dataset' => 'PD_PRICES'],
    ],

];
