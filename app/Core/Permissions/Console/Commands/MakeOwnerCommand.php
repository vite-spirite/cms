<?php

namespace App\Core\Permissions\Console\Commands;

use App\Core\Auth\Models\User;
use Illuminate\Console\Command;

class MakeOwnerCommand extends Command
{
    protected $name = 'permissions:owner';
    protected $description = 'Create a new permission group';

    protected $signature = 'permissions:owner {action : make|revoke|list} {email?}';

    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'make':
                return $this->makeOwner();
            case 'revoke':
                return $this->revokeOwner();
            case 'list':
                return $this->listOwner();
        }
    }

    private function makeOwner(): int
    {
        $email = $this->argument('email') ?? $this->ask('Email');

        $user = \App\Core\Auth\Models\User::where('email', $email)->first();
        if (!$user) {
            $this->error('User not found');
            return 1;
        }

        if ($user->isOwner()) {
            $this->error('User is already owned');
            return 0;
        }

        $owner = User::where('is_owner', true)->first();
        if ($owner) {
            $this->warn('User is already owned');

            if (!$this->confirm('Are you sure you want to do that?')) {
                return 0;
            }
        }

        $user->is_owner = true;
        $user->save();
        $this->info("User {$user->name} as owner");

        return 0;
    }

    private function revokeOwner(): int
    {
        $email = $this->argument('email') ?? $this->ask('Email');
        $user = \App\Core\Auth\Models\User::where('email', $email)->first();

        if (!$user) {
            $this->error('User not found');
            return 1;
        }

        if (!$user->isOwner()) {
            $this->error('User is not owned');
            return 1;
        }

        $user->is_owner = false;
        $user->save();

        $this->info("User {$user->name} as revoked owner");
        return 0;
    }

    private function listOwner()
    {
        $users = User::where('is_owner', true)->get();
        $this->table(['id', 'name', 'email'], $users->map(function ($user) {
            return [
                $user->id,
                $user->name,
                $user->email,
            ];
        }));

        return 0;
    }
}
