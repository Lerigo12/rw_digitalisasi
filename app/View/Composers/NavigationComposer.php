<?php

namespace App\View\Composers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NavigationComposer
{
    public function compose(View $view): void
    {
        $user = Auth::user();
        $roles = $user ? $user->roles : collect();
        $view->with('userRoles', $roles);
    }
}
