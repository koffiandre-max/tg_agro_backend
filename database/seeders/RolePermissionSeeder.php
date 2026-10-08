<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'Voir le tableau de bord', 'slug' => 'dashboard.view', 'description' => 'Accès au tableau de bord'],
            ['name' => 'Voir les clients', 'slug' => 'clients.view', 'description' => 'Consulter la liste des clients'],
            ['name' => 'Créer des clients', 'slug' => 'clients.create', 'description' => 'Créer un nouveau client'],
            ['name' => 'Modifier les clients', 'slug' => 'clients.edit', 'description' => 'Modifier un client existant'],
            ['name' => 'Supprimer les clients', 'slug' => 'clients.delete', 'description' => 'Supprimer un client'],

            ['name' => 'Voir les fermes', 'slug' => 'farms.view', 'description' => 'Consulter les fermes'],
            ['name' => 'Créer des fermes', 'slug' => 'farms.create', 'description' => 'Créer une ferme'],
            ['name' => 'Modifier les fermes', 'slug' => 'farms.edit', 'description' => 'Modifier une ferme'],
            ['name' => 'Supprimer les fermes', 'slug' => 'farms.delete', 'description' => 'Supprimer une ferme'],
            ['name' => 'Exporter PDF fermes', 'slug' => 'farms.pdf', 'description' => 'Générer le PDF d\'une ferme'],

            ['name' => 'Voir les missions', 'slug' => 'missions.view', 'description' => 'Consulter les missions'],
            ['name' => 'Créer des missions', 'slug' => 'missions.create', 'description' => 'Créer une mission'],
            ['name' => 'Modifier les missions', 'slug' => 'missions.edit', 'description' => 'Modifier une mission'],
            ['name' => 'Supprimer les missions', 'slug' => 'missions.delete', 'description' => 'Supprimer une mission'],
            ['name' => 'Compléter une mission', 'slug' => 'missions.complete', 'description' => 'Marquer une mission comme complétée'],

            ['name' => 'Voir les rapports', 'slug' => 'reports.view', 'description' => 'Consulter les rapports'],
            ['name' => 'Créer des rapports', 'slug' => 'reports.create', 'description' => 'Créer un rapport'],
            ['name' => 'Valider les rapports', 'slug' => 'reports.validate', 'description' => 'Valider un rapport'],
            ['name' => 'Supprimer les rapports', 'slug' => 'reports.delete', 'description' => 'Supprimer un rapport'],

            ['name' => 'Voir les photos', 'slug' => 'photos.view', 'description' => 'Consulter les photos'],
            ['name' => 'Téléverser des photos', 'slug' => 'photos.upload', 'description' => 'Ajouter des photos'],
            ['name' => 'Supprimer des photos', 'slug' => 'photos.delete', 'description' => 'Supprimer des photos'],

            ['name' => 'Voir les données', 'slug' => 'data.view', 'description' => 'Consulter les saisies'],
            ['name' => 'Créer des données', 'slug' => 'data.create', 'description' => 'Créer une saisie'],
            ['name' => 'Modifier des données', 'slug' => 'data.edit', 'description' => 'Modifier une saisie'],
            ['name' => 'Supprimer des données', 'slug' => 'data.delete', 'description' => 'Supprimer une saisie'],

            ['name' => 'Voir les utilisateurs', 'slug' => 'users.view', 'description' => 'Consulter les utilisateurs'],
            ['name' => 'Créer des utilisateurs', 'slug' => 'users.create', 'description' => 'Créer un utilisateur'],
            ['name' => 'Modifier des utilisateurs', 'slug' => 'users.edit', 'description' => 'Modifier un utilisateur'],
            ['name' => 'Supprimer des utilisateurs', 'slug' => 'users.delete', 'description' => 'Supprimer un utilisateur'],

            ['name' => 'Gérer les rôles', 'slug' => 'roles.manage', 'description' => 'Créer/modifier les rôles'],
            ['name' => 'Gérer les permissions', 'slug' => 'permissions.manage', 'description' => 'Créer/modifier les permissions'],

            ['name' => 'Voir les paramètres', 'slug' => 'settings.view', 'description' => 'Consulter les paramètres'],
            ['name' => 'Modifier les paramètres', 'slug' => 'settings.edit', 'description' => 'Modifier les paramètres système'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['slug' => $permission['slug']], $permission);
        }

        $roles = [
            'admin' => [
                'name' => 'Admin',
                'description' => 'Administrateur avec tous les droits',
                'permissions' => Permission::pluck('id')->all(),
            ],
            'technician' => [
                'name' => 'Technicien',
                'description' => 'Technicien terrain',
                'permissions' => Permission::whereIn('slug', [
                    'dashboard.view',
                    'clients.view',
                    'farms.view',
                    'farms.pdf',
                    'missions.view',
                    'missions.complete',
                    'reports.view',
                    'reports.create',
                    'photos.view',
                    'photos.upload',
                    'data.view',
                    'data.create',
                    'data.edit',
                ])->pluck('id')->all(),
            ],
            'client' => [
                'name' => 'Client',
                'description' => 'Client',
                'permissions' => Permission::whereIn('slug', [
                    'dashboard.view',
                    'farms.view',
                    'farms.create',
                    'farms.edit',
                    'reports.view',
                    'photos.view',
                ])->pluck('id')->all(),
            ],
        ];

        foreach ($roles as $slug => $data) {
            $role = Role::updateOrCreate(['slug' => $slug], [
                'name' => $data['name'],
                'description' => $data['description'],
            ]);

            $role->permissions()->sync($data['permissions']);
        }
    }
}
