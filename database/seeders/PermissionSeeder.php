<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\permissions\Permission;
use App\Models\permissions\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'Saisir de produits', 'slug' => 'create-product']);
        Permission::create(['name' => 'Edition de produits', 'slug' => 'edit-product']);
        Permission::create(['name' => 'Affichage des produits', 'slug' => 'list-product']);
        Permission::create(['name' => 'Supression des produits', 'slug' => 'delete-product']);

        Permission::create(['name' => 'Saisir utilisateur', 'slug' => 'create-user']);
        Permission::create(['name' => 'Edition utilisateur', 'slug' => 'edit-user']);
        Permission::create(['name' => 'Affichage des utilisateurs', 'slug' => 'list-user']);
        Permission::create(['name' => 'Supression des utilisateurs', 'slug' => 'delete-user']);

        Permission::create(['name' => 'Saisir utilisateur', 'slug' => 'create-role']);
        Permission::create(['name' => 'Edition utilisateur', 'slug' => 'edit-role']);
        Permission::create(['name' => 'Affichage des utilisateurs', 'slug' => 'list-role']);
        Permission::create(['name' => 'Supression des utilisateurs', 'slug' => 'delete-role']);

        $adminRole = Role::create(['name' => 'Admin','slug' => 'admin']);
        $editorRole = Role::create(['name' => 'Exploitant', 'slug' => 'exploitant']);

        /* $adminRole->givePermissionTo([
            'create-users',
            'edit-users',
            'delete-users',
            'create-blog-posts',
            'edit-blog-posts',
            'delete-blog-posts',
        ]);

        $editorRole->givePermissionTo([
            'create-blog-posts',
            'edit-blog-posts',
            'delete-blog-posts',
        ]); */
    }
}
