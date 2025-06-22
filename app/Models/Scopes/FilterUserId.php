<?php

namespace App\Models\Scopes;

use App\Exceptions\Auth\NotAuthenticatedException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class FilterUserId implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (!Auth::check())
            throw new NotAuthenticatedException('user is not authenticated');

        $builder->whereUserId(Auth::user()->id);
    }
}
