<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Role extends Model
{
    use Auditable;

    protected $fillable = ['name', 'description', 'permissions'];
    protected $casts = ['permissions' => 'array'];

    /** Accept both ordinary JSON arrays and legacy arrays encoded twice by older seed data. */
    public static function permissionList(mixed $value): array
    {
        for ($i = 0; $i < 2 && is_string($value); $i++) {
            $value = json_decode($value, true);
        }

        if (!is_array($value) || !array_is_list($value)) {
            return [];
        }

        return array_values(array_unique(array_filter($value, fn ($item) => is_string($item) && $item !== '')));
    }

    public static function permissionsFor(AdminUser $user): array
    {
        $own = self::permissionList($user->permissions);

        return $own !== [] ? $own : self::permissionList($user->getRelationValue('role')?->permissions);
    }

    public function users() { return $this->hasMany(AdminUser::class); }
}
