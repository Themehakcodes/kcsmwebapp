<?php

namespace App\Http\Controllers\Admin;

use App\Models\SidebarItem;
use Illuminate\Support\Facades\Auth;

class SidebarController
{
    public function index()
    {
        // Get the authenticated user
        $user = Auth::user();

        if ($user->is_superadmin) {
            // Fetch all sidebar items if the user is a superadmin
            $sidebarItems = SidebarItem::orderBy('order')->with('permission')->get();
        } else {
            // Fetch sidebar items based on user permissions
            $sidebarItems = SidebarItem::with('permission')
                ->whereHas('permission', function ($query) use ($user) {
                    $query->whereIn('title', $this->getUserPermissionTitles($user));
                })
                ->orderBy('order')
                ->get();
        }

        // Return sidebar items to the Blade file
        return $sidebarItems;
    }

    // Helper function to get user permission titles
    private function getUserPermissionTitles($user)
    {
        // Fetch the user's permissions based on roles, ensuring soft deletes are handled
        $permissions = $user->roles()
            ->join('permission_roles', function ($join) {
                $join->on('roles.id', '=', 'permission_roles.role_id')
                     ->whereNull('permission_roles.deleted_at');
            })
            ->join('permissions', function ($join) {
                $join->on('permission_roles.permission_id', '=', 'permissions.id')
                     ->whereNull('permissions.deleted_at');
            })
            ->pluck('permissions.title');

        return $permissions ?? collect(); // Return an empty collection if null
    }
}
