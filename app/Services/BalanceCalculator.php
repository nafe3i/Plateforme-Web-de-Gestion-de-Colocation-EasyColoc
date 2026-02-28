<?php

namespace App\Services;

use App\Models\Colocation;
use App\Models\Membership;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BalanceCalculator
{
    public static function recalculate(Colocation $colocation): void
    {
        $allMemberships = $colocation->memberships()
            ->get(['user_id', 'joined_at', 'left_at']);

        $activeMemberships = $colocation->memberships()
            ->whereNull('left_at')
            ->get(['user_id', 'manual_adjustment']);

        if ($activeMemberships->isEmpty()) {
            return;
        }

        // Base balance = manual adjustment (used when owner absorbs debt after member removal).
        $balances = [];
        foreach ($activeMemberships as $membership) {
            $balances[(int) $membership->user_id] = (float) $membership->manual_adjustment;
        }

        $expenses = $colocation->expenses()->get(['paid_by', 'amount', 'date']);
        foreach ($expenses as $expense) {
            $totalAmount = (float) $expense->amount;
            $paidBy = (int) $expense->paid_by;

            $participantIds = $allMemberships
                ->filter(fn(Membership $membership) => self::isActiveOnDate($membership, $expense->date))
                ->pluck('user_id')
                ->map(fn($id) => (int) $id)
                ->values();

            $participantCount = $participantIds->count();
            if ($participantCount === 0) {
                continue;
            }

            $sharePerPerson = $totalAmount / $participantCount;

            foreach ($participantIds as $memberId) {
                // We only maintain balances for current active members.
                if (!array_key_exists($memberId, $balances)) {
                    continue;
                }

                if ($memberId === $paidBy) {
                    $delta = -($totalAmount - $sharePerPerson);
                } else {
                    $delta = $sharePerPerson;
                }

                $balances[$memberId] += $delta;
            }
        }

        $payments = $colocation->payments()->get(['from_user_id', 'to_user_id', 'amount']);
        foreach ($payments as $payment) {
            $fromId = (int) $payment->from_user_id;
            $toId = (int) $payment->to_user_id;
            $amount = (float) $payment->amount;

            if (array_key_exists($fromId, $balances)) {
                $balances[$fromId] -= $amount;
            }

            if (array_key_exists($toId, $balances)) {
                $balances[$toId] += $amount;
            }
        }

        foreach ($balances as $userId => $balance) {
            DB::table('memberships')
                ->where('colocation_id', $colocation->id)
                ->where('user_id', $userId)
                ->whereNull('left_at')
                ->update(['balance' => round($balance, 2)]);
        }
    }

    public static function getSettlements(Colocation $colocation): array
    {
        $activeMembers = $colocation->activeMembers()->get();

        $debtors = [];
        $creditors = [];

        foreach ($activeMembers as $member) {
            $balance = (float) $member->pivot->balance;

            if ($balance > 0.01) {
                $debtors[] = ['user' => $member, 'amount' => $balance];
            } elseif ($balance < -0.01) {
                $creditors[] = ['user' => $member, 'amount' => abs($balance)];
            }
        }

        $settlements = [];

        usort($debtors, fn($a, $b) => $b['amount'] <=> $a['amount']);
        usort($creditors, fn($a, $b) => $b['amount'] <=> $a['amount']);

        $i = 0;
        $j = 0;

        while ($i < count($debtors) && $j < count($creditors)) {
            $debtor = $debtors[$i];
            $creditor = $creditors[$j];

            $amount = min($debtor['amount'], $creditor['amount']);
            $roundedAmount = round($amount, 2);

            if ($roundedAmount <= 0) {
                break;
            }

            $settlements[] = [
                'from' => $debtor['user'],
                'from_id' => $debtor['user']->id,
                'to' => $creditor['user'],
                'to_id' => $creditor['user']->id,
                'amount' => $roundedAmount,
            ];

            $debtors[$i]['amount'] -= $amount;
            $creditors[$j]['amount'] -= $amount;

            if ($debtors[$i]['amount'] < 0.01) {
                $i++;
            }
            if ($creditors[$j]['amount'] < 0.01) {
                $j++;
            }
        }

        return $settlements;
    }

    private static function isActiveOnDate(Membership $membership, $date): bool
    {
        $expenseDate = $date instanceof \DateTimeInterface ? Carbon::instance($date) : Carbon::parse($date);

        if ($membership->joined_at && $membership->joined_at->gt($expenseDate)) {
            return false;
        }

        if ($membership->left_at && !$membership->left_at->gt($expenseDate)) {
            return false;
        }

        return true;
    }
}
