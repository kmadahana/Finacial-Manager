<?php

namespace App\Support;

use App\Models\CycleSummary;
use App\Models\User;
use Illuminate\Support\Carbon;

/**
 * Carry-forward balance ledger.
 *
 * Leftover money (planned income − expenses) rolls into the next pay cycle
 * instead of resetting. Each COMPLETED cycle is materialised into
 * `cycle_summaries` with its opening/closing balance; the opening balance of
 * a cycle is the closing balance of the one before it, anchored to the user's
 * `opening_balance` as of `opening_balance_at`.
 *
 * Finalised rows are re-derived from live data on each sync (self-correcting
 * if past transactions are edited). Salary is treated as the current constant;
 * `salary_snapshot` records what was used.
 */
class CycleLedger
{
    /** The first planning cycle [year, month] we start carrying from. */
    public static function anchorMonth(User $user): array
    {
        // An explicit starting-balance date wins; otherwise carry only from registration.
        if ($user->opening_balance_at) {
            return MonthlyFinance::currentPlanningMonth($user, Carbon::parse($user->opening_balance_at));
        }

        return MonthlyFinance::registrationMonth($user);
    }

    /** Materialise every completed cycle from the anchor up to (not incl.) the current one. */
    public static function sync(User $user): void
    {
        [$cy, $cm] = MonthlyFinance::currentPlanningMonth($user);
        [$ay, $am] = self::anchorMonth($user);

        $current = Carbon::create($cy, $cm, 1);
        $cursor  = Carbon::create($ay, $am, 1);
        $opening = (float) ($user->opening_balance ?? 0);

        // Drop any rows outside [anchor, current) — e.g. left behind if the
        // anchor moved, or the current (not-yet-complete) cycle.
        CycleSummary::where('user_id', $user->id)
            ->whereRaw('(year * 12 + month) < ? OR (year * 12 + month) >= ?', [
                $ay * 12 + $am,
                $cy * 12 + $cm,
            ])
            ->delete();

        while ($cursor->lt($current)) {
            $fin      = MonthlyFinance::for($user, $cursor->year, $cursor->month);
            $income   = $fin->plannedIncome;
            $expenses = $fin->expenses;
            $closing  = $opening + $income - $expenses;

            CycleSummary::updateOrCreate(
                ['user_id' => $user->id, 'year' => $cursor->year, 'month' => $cursor->month],
                [
                    'opening_balance' => $opening,
                    'income'          => $income,
                    'expenses'        => $expenses,
                    'closing_balance' => $closing,
                    'salary_snapshot' => $fin->salary,
                    'finalized_at'    => now(),
                ]
            );

            $opening = $closing;
            $cursor->addMonth();
        }
    }

    /** Balance carried INTO planning cycle (year, month). */
    public static function openingBalanceFor(User $user, int $year, int $month): float
    {
        self::sync($user);

        $prev = Carbon::create($year, $month, 1)->subMonth();
        $row  = CycleSummary::where('user_id', $user->id)
            ->where('year', $prev->year)
            ->where('month', $prev->month)
            ->first();

        return $row ? (float) $row->closing_balance : (float) ($user->opening_balance ?? 0);
    }
}
