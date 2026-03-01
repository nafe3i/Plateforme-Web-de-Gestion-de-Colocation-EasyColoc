<?php

namespace App\Services;

use App\Models\Colocation;
use Illuminate\Support\Facades\DB;

class BalanceCalculator
{
    /**
     * Recalculer les balances de tous les membres actifs d'une colocation
     */
    public static function recalculate(Colocation $colocation)
    {
        // Récupérer tous les membres actifs (qui n'ont pas quitté)
        $activeMembers = $colocation->activeMembers;
        $memberCount = $activeMembers->count();

        // Si pas de membres, on arrête
        if ($memberCount === 0) {
            return;
        }

        // Réinitialiser toutes les balances à 0
        foreach ($activeMembers as $member) {
            DB::table('memberships')
                ->where('colocation_id', $colocation->id)
                ->where('user_id', $member->id)
                ->whereNull('left_at')
                ->update(['balance' => 0]);
        }

        // Récupérer toutes les dépenses de la colocation
        $expenses = $colocation->expenses;

        // Pour chaque dépense, calculer la part de chacun
        foreach ($expenses as $expense) {
            $totalAmount = $expense->amount;
            $paidBy = $expense->paid_by;
            
            // Calculer la part par personne
            $sharePerPerson = $totalAmount / $memberCount;

            // Mettre à jour la balance de chaque membre
            foreach ($activeMembers as $member) {
                // Récupérer la balance actuelle
                $currentBalance = DB::table('memberships')
                    ->where('colocation_id', $colocation->id)
                    ->where('user_id', $member->id)
                    ->whereNull('left_at')
                    ->value('balance');

                // Calculer la nouvelle balance
                if ($member->id == $paidBy) {
                    // Le payeur a payé pour tout le monde
                    // Il doit recevoir: (montant total - sa part)
                    $newBalance = $currentBalance - ($totalAmount - $sharePerPerson);
                } else {
                    // Les autres membres doivent leur part
                    $newBalance = $currentBalance + $sharePerPerson;
                }

                // Mettre à jour la balance dans la base de données
                DB::table('memberships')
                    ->where('colocation_id', $colocation->id)
                    ->where('user_id', $member->id)
                    ->whereNull('left_at')
                    ->update(['balance' => round($newBalance, 2)]);
            }
        }
    }

    /**
     * Calculer qui doit payer qui (simplification des dettes)
     */
    public static function getSettlements(Colocation $colocation)
    {
        $activeMembers = $colocation->activeMembers;
        
        // Séparer les membres en deux groupes:
        // - Ceux qui doivent de l'argent (balance positive)
        // - Ceux à qui on doit de l'argent (balance négative)
        $debtors = [];    // Ceux qui doivent
        $creditors = [];  // Ceux à qui on doit

        foreach ($activeMembers as $member) {
            $balance = $member->pivot->balance;
            
            if ($balance > 0.01) {
                // Ce membre doit de l'argent
                $debtors[] = [
                    'user' => $member,
                    'amount' => $balance
                ];
            } elseif ($balance < -0.01) {
                // On doit de l'argent à ce membre
                $creditors[] = [
                    'user' => $member,
                    'amount' => abs($balance) // Valeur absolue pour avoir un montant positif
                ];
            }
        }

        // Trier par montant décroissant
        usort($debtors, function($a, $b) {
            return $b['amount'] <=> $a['amount'];
        });
        usort($creditors, function($a, $b) {
            return $b['amount'] <=> $a['amount'];
        });

        // Calculer les paiements simplifiés
        $settlements = [];
        $i = 0; // Index pour debtors
        $j = 0; // Index pour creditors

        while ($i < count($debtors) && $j < count($creditors)) {
            $debtor = $debtors[$i];
            $creditor = $creditors[$j];

            // Le montant à payer est le minimum entre ce que doit le débiteur
            // et ce que doit recevoir le créditeur
            $amount = min($debtor['amount'], $creditor['amount']);

            // Ajouter ce paiement à la liste
            $settlements[] = [
                'from' => $debtor['user'],
                'to' => $creditor['user'],
                'amount' => round($amount, 2),
            ];

            // Mettre à jour les montants restants
            $debtors[$i]['amount'] -= $amount;
            $creditors[$j]['amount'] -= $amount;

            // Si le débiteur a tout payé, passer au suivant
            if ($debtors[$i]['amount'] < 0.01) {
                $i++;
            }
            
            // Si le créditeur a tout reçu, passer au suivant
            if ($creditors[$j]['amount'] < 0.01) {
                $j++;
            }
        }

        return $settlements;
    }
}
