<?php

namespace App\Policies;

use App\Models\{Order, User};

class OrderPolicy
{
    public function view(User $user, Order $order): bool
    {   
        // dono do pedido pode ver
        return $order->user_id === $user->id;
    }

    public function update(User $user, Order $order): bool
    {
        // dono pode atualizar enquanto não enviado; admin também (se houver RBAC)
        $isOwner = $order->user_id === $user->id;
        $isEditable = in_array($order->status, ['pending', 'awaiting_payment', 'paid']);
        return $isOwner && $isEditable;
    }
}
