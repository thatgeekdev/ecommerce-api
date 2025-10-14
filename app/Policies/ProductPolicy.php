<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    public function manage(User $user): bool
    {
        // Spatie Permission:
        if (method_exists($user, 'hasPermissionTo')) {
            return $user->hasPermissionTo('manage-products');
        }
        dd('ok');
        // Fallback: ADMIN_EMAILS=admin@exemplo.com,admin2@exemplo.com
        $adminEmails = collect(explode(',', (string) env('ADMIN_EMAILS')))->map(fn($e)=>trim($e))->filter();
        return $adminEmails->contains($user->email);
    }
    
}
