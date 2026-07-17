<?php

namespace App\View\Composers;

use App\Support\Navigation;
use Illuminate\View\View;

class NavigationComposer
{
    public function compose(View $view)
    {
        // On récupère le menu via notre classe Support
        $view->with('navigation', Navigation::filtered());
    }
}
