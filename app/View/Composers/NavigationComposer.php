<?php

namespace App\View\Composers;

use App\Support\Navigation;
use Illuminate\View\View;

class NavigationComposer
{
    public function compose(View $view)
    {
        // Menu groupé par catégorie pour la sidebar principale
        $view->with('navigation', Navigation::grouped());
    }
}
