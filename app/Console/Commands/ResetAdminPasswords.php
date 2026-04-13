<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetAdminPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:admin-passwords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset passwords for the three core admin accounts';

    /**
     * Accounts to reset: email => plain-text password.
     *
     * @var array<string, string>
     */
    private array $accounts = [
        'admin@picpic.com' => 'picpic123',
        'owner@picpic.com' => 'picpic123',
        'kasir@picpic.com' => 'picpic123',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Resetting admin passwords...');
        $this->newLine();

        foreach ($this->accounts as $email => $password) {
            $user = User::where('email', $email)->first();

            if (! $user) {
                $this->warn("  SKIPPED  {$email} — user not found.");
                continue;
            }

            $user->password = Hash::make($password);
            $user->save();

            $this->line("  <fg=green>UPDATED</> {$email} — password reset successfully.");
        }

        $this->newLine();
        $this->info('Done.');

        return self::SUCCESS;
    }
}
