<?php

namespace Tests\Unit;

use App\Models\Role;
use PHPUnit\Framework\TestCase;

class RolePermissionListTest extends TestCase
{
    public function test_normal_and_legacy_double_encoded_permissions_are_normalized(): void
    {
        $this->assertSame(['dashboard', 'all_forms'], Role::permissionList(['dashboard', 'all_forms']));
        $this->assertSame(['dashboard', 'all_forms'], Role::permissionList(json_encode(json_encode(['dashboard', 'all_forms']))));
        $this->assertSame([], Role::permissionList('{"dashboard":true}'));
        $this->assertSame([], Role::permissionList('not json'));
    }
}
