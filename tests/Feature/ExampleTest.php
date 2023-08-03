<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\Permission;
use App\Models\User;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_example(): void
    {
        // Create a permission for viewing inactive users
        $permission = Permission::query()->create(['name' => 'viewInactiveUsers']);

        // Create a superadmin user who can see inactive users
        $superadmin = User::query()->create(['name' => 'Superadmin']);
        $superadmin->permissions()->attach($permission->id);

        // Create an admin user who cannot see inactive users
        $admin = User::query()->create(['name' => 'Admin']);

        // Create a pair of users - one active, one inactive
        $active = User::query()->create(['name' => 'Active user', 'is_active' => true]);
        $inactive = User::query()->create(['name' => 'Inactive user', 'is_active' => false]);

        // Assign both users to the same group
        $group = Group::query()->create(['name' => 'Group foo']);
        $group->users()->attach([$active->id, $inactive->id]);

        $this->actingAs($superadmin);
        // All users in group
        $this->assertEquals(2, $group->users()->count());
        // Visible users in group - same number because superadmin can see all users
        $this->assertEquals(2, $group->visibleUsers()->count());
        // Same as above but using withCount
        $this->assertEquals(
            2,
            Group::query()
                ->where('id', $group->id)
                ->withCount('visibleUsers')
                ->first()
                ->visible_users_count
        );

        $this->actingAs($admin);
        // All users in group
        $this->assertEquals(2, $group->users()->count());
        // Visible users in group - only one because admin cannot see inactive users
        $this->assertEquals(1, $group->visibleUsers()->count());
        // Same as above but using withCount
        // This fails - admin will see a count of 2 instead of 1
//        $this->assertEquals(
//            1,
//            Group::query()
//                ->where('id', $group->id)
//                ->withCount('visibleUsers')
//                ->first()
//                ->visible_users_count
//        );
    }
}
