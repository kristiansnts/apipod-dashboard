<?php

namespace App\Console\Commands;

use App\Models\Organization;
use App\Models\ApiKey;
use App\Models\TokenLedger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuotaResetCommand extends Command
{
    protected $signature = 'quota:reset';
    protected $description = 'Reset token quotas for organizations that have reached their billing cycle';

    public function handle(): int
    {
        $orgs = Organization::with('plan')
            ->whereNotNull('quota_reset_at')
            ->where('quota_reset_at', '<=', now())
            ->whereNotNull('plan_id')
            ->get();

        if ($orgs->isEmpty()) {
            $this->info('No organizations to reset.');
            return self::SUCCESS;
        }

        $resetCount = 0;
        $errorCount = 0;

        foreach ($orgs as $org) {
            try {
                DB::transaction(function () use ($org) {
                    // Lock the org row to prevent conflicts with concurrent commits
                    $org = Organization::lockForUpdate()->findOrFail($org->id);

                    $previousBalance = $org->token_balance;
                    $newBalance = $org->plan->token_quota;

                    // Refill token balance
                    $org->update([
                        'token_balance' => $newBalance,
                        'quota_reset_at' => now()->addDays($org->plan->duration_days ?? 30),
                        'next_billing_at' => now()->addDays($org->plan->duration_days ?? 30),
                    ]);

                    // Reset per-key used_tokens
                    ApiKey::where('org_id', $org->id)->update(['used_tokens' => 0]);

                    // Record ledger entry
                    TokenLedger::create([
                        'org_id' => $org->id,
                        'type' => 'reset',
                        'input_tokens' => 0,
                        'output_tokens' => 0,
                        'cost_usd' => 0,
                        'balance_after' => $newBalance,
                        'description' => "Quota reset. Previous balance: {$previousBalance}. New balance: {$newBalance}.",
                    ]);
                });

                $resetCount++;
                $this->info("Reset org #{$org->id} ({$org->name}) → {$org->plan->token_quota} tokens");
            } catch (\Exception $e) {
                $errorCount++;
                $this->error("Failed to reset org #{$org->id}: {$e->getMessage()}");
                Log::error("Quota reset failed for org #{$org->id}", ['error' => $e->getMessage()]);
            }
        }

        $this->info("Done. Reset: {$resetCount}, Errors: {$errorCount}");

        return $errorCount > 0 ? self::FAILURE : self::SUCCESS;
    }
}
