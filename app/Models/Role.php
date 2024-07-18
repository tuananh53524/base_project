<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    public static function getRoles($id = 0)
    {
        $query = self::where('status', config('app.status.active'));

        if ($id) {
            $query->where('id', $id);
        }

        $roles = $query->limit(20)->get();

        return $roles;
    }
}
