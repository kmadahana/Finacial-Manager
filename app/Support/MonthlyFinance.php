<?php

namespace App\Support;

use App\Models\Budget;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Carbon;

/**
 * Single source of truth for a user's monthly financial figures.
 *
 * Money is planned on a PAY CYCLE, not the calendar month. In the user's
 * region salary lands ~25th–10th and funds the *upcoming* month. So a
 * planning month (year, month) covers the window:
 *
 *     [ cutoff-day of the previous month , cutoff-day of this month )
 *
 * e.g. with cutoff 25, "June 2026" runs 25 May → 24 June. The active
 * planning month is whichever window contains today.
 *
 * Both the Dashboard and the Budget page pull from here so the numbers
 * (planned income, expenses, budget, savings, pace) never diverge.
 */
class MonthlyFinance
{
    public const DEFAULT_CYCLE_DAY = 25;

    public function __construct(
        public readonly int $year,
        public readonly int $month,
        public readonly int $cycleDay,
        public readonly Carbon $windowStart,   // inclusive
        public readonly Carbon $windowEnd,     // exclusive
        public readonly float $salary,
        public readonly float $incomeEntries,
        public readonly float $plannedIncome,
        public readonly float $expenses,
        public readonly float $budgetSet,
        public readonly float $remaining,
        public readonly float $projectedSaving,
        public readonly float $totalBalance,
        public readonly int $dayOfMonth,
        public readonly int $daysInMonth,
        public readonly float $monthProgress,
        public readonly float $expectedByNow,
        public readonly bool $onTrack,
        public readonly float $spentPct,
        public readonly bool $isCurrentMonth,
        public readonly bool $isFutureMonth,
    ) {}

    /** Pay-cycle cutoff day for a user (1–31). Short months clamp to their last day. */
    public static function cycleDay(User $user): int
    {
        $d = (int) ($user->pay_cycle_day ?? self::DEFAULT_CYCLE_DAY);
        return max(1, min(31, $d));
    }

    /** The planning [year, month] whose pay-cycle window contains $today. */
    public static function currentPlanningMonth(User $user, ?Carbon $today = null): array
    {
        $today  = ($today ?? now())->copy()->startOfDay();
        $cut    = self::cycleDay($user);
        // A cutoff like 31 falls on the last day of shorter months (28/29/30).
        $effectiveCut = min($cut, $today->daysInMonth);
        $anchor = Carbon::create($today->year, $today->month, 1);

        if ($today->day >= $effectiveCut) {
            $anchor->addMonth();
        }

        return [$anchor->year, $anchor->month];
    }

    /** The first planning [year, month] with real data — the user's registration cycle. */
    public static function registrationMonth(User $user): array
    {
        return self::currentPlanningMonth($user, $user->created_at ? Carbon::parse($user->created_at) : now());
    }

    public static function for(User $user, int $year, int $month): self
    {
        $cut = self::cycleDay($user);

        $firstThis = Carbon::create($year, $month, 1);
        $firstPrev = $firstThis->copy()->subMonth();

        $windowStart = $firstPrev->copy()->day(min($cut, $firstPrev->daysInMonth))->startOfDay();
        $windowEnd   = $firstThis->copy()->day(min($cut, $firstThis->daysInMonth))->startOfDay(); // exclusive

        $today          = now()->copy()->startOfDay();
        $isFutureMonth  = $today->lt($windowStart);
        $isPastMonth    = $today->gte($windowEnd);
        $isCurrentMonth = ! $isFutureMonth && ! $isPastMonth;

        $daysInMonth = (int) round($windowStart->diffInDays($windowEnd));
        $dayOfMonth  = match (true) {
            $isFutureMonth => 0,
            $isPastMonth   => $daysInMonth,
            default        => (int) round($windowStart->diffInDays($today)) + 1,
        };
        $monthProgress = $daysInMonth > 0 ? round(($dayOfMonth / $daysInMonth) * 100, 1) : 0;

        // No earnings are assumed before the user registered — cycles before the
        // registration cycle get zero salary (transactions still count if present).
        [$ry, $rm] = self::registrationMonth($user);
        $beforeRegistration = Carbon::create($year, $month, 1)->lt(Carbon::create($ry, $rm, 1));
        $salary = $beforeRegistration ? 0.0 : (float) ($user->salary ?? 0);

        $incomeEntries = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'income')
            ->where('transaction_date', '>=', $windowStart)
            ->where('transaction_date', '<', $windowEnd)
            ->sum('amount');

        $expenses = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->where('transaction_date', '>=', $windowStart)
            ->where('transaction_date', '<', $windowEnd)
            ->sum('amount');

        $budgetSet = (float) Budget::where('user_id', $user->id)
            ->where('year', $year)
            ->where('month', $month)
            ->sum('planned_amount');

        $plannedIncome = $salary + $incomeEntries;
        $remaining     = $budgetSet - $expenses;

        // Per product decision: savings / projected month-end balance are
        // forward-looking against actual spend — what's left of planned
        // income after the expenses booked so far this cycle.
        $netPlanned      = $plannedIncome - $expenses;
        $projectedSaving = $netPlanned;
        $totalBalance    = $netPlanned;

        $expectedByNow = ($budgetSet > 0 && $daysInMonth > 0)
            ? ($dayOfMonth / $daysInMonth) * $budgetSet
            : 0;
        $onTrack = $isFutureMonth
            ? true
            : ($budgetSet <= 0.0 ? true : $expenses <= $expectedByNow);
        $spentPct = $budgetSet > 0 ? min(100, ($expenses / $budgetSet) * 100) : 0;

        return new self(
            year: $year,
            month: $month,
            cycleDay: $cut,
            windowStart: $windowStart,
            windowEnd: $windowEnd,
            salary: $salary,
            incomeEntries: $incomeEntries,
            plannedIncome: $plannedIncome,
            expenses: $expenses,
            budgetSet: $budgetSet,
            remaining: $remaining,
            projectedSaving: $projectedSaving,
            totalBalance: $totalBalance,
            dayOfMonth: $dayOfMonth,
            daysInMonth: $daysInMonth,
            monthProgress: $monthProgress,
            expectedByNow: $expectedByNow,
            onTrack: $onTrack,
            spentPct: $spentPct,
            isCurrentMonth: $isCurrentMonth,
            isFutureMonth: $isFutureMonth,
        );
    }

    /** Human label for the cycle window, e.g. "25 May – 24 Jun". */
    public function windowLabel(): string
    {
        return $this->windowStart->format('j M') . ' – ' . $this->windowEnd->copy()->subDay()->format('j M');
    }
}
