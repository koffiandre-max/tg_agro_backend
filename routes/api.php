<?php

use Illuminate\Support\Facades\Route;

// Routes API de messagerie supprimées - utilisation via les routes web uniquement
// Ces routes sont disponibles via /chat/messages (web) et /portail/messages (web)

// L'endpoint des prix FAOSTAT (Côte d'Ivoire) est exposé via web.php
// sous le nom 'api.market-prices.cote-divoire' pour éviter le middleware API.