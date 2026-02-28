<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Colocation;
use App\Models\Expense;
use App\Models\Payment;
use App\Services\BalanceCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;

class ExpenseController extends Controller
{
    public function index(Colocation $colocation, Request $request)
    {
        $this->ensureMember($colocation);

        $month = $request->get('month', 'all');
        $query = $colocation->expenses()->with(['payer', 'category']);

        if ($month !== 'all') {
            if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
                return redirect()->route('expenses.index', $colocation)
                    ->with('error', 'Format de mois invalide.');
            }

            [$year, $monthNumber] = explode('-', $month);

            $query->whereYear('date', (int) $year)
                ->whereMonth('date', (int) $monthNumber);
        }

        $expenses = $query->orderBy('date', 'desc')->get();

        $months = $colocation->expenses()
            ->orderBy('date', 'desc')
            ->get(['date'])
            ->map(fn($expense) => $expense->date->format('Y-m'))
            ->unique()
            ->values();

        BalanceCalculator::recalculate($colocation);
        $settlements = BalanceCalculator::getSettlements($colocation);

        return view('expenses.index', compact('colocation', 'expenses', 'months', 'month', 'settlements'));
    }

    public function create(Colocation $colocation)
    {
        $this->ensureMember($colocation);

        $categories = Category::orderBy('name')->get();
        $members = $colocation->activeMembers()->get();

        return view('expenses.create', compact('colocation', 'categories', 'members'));
    }

    public function store(Request $request, Colocation $colocation)
    {
        $this->ensureMember($colocation);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'category_id' => 'required|exists:categories,id',
            'paid_by' => [
                'required',
                'integer',
                $this->activeMemberRule($colocation),
            ],
            'description' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            Expense::create([
                'colocation_id' => $colocation->id,
                'paid_by' => $validated['paid_by'],
                'category_id' => $validated['category_id'],
                'title' => $validated['title'],
                'amount' => $validated['amount'],
                'date' => $validated['date'],
                'description' => $validated['description'],
            ]);

            BalanceCalculator::recalculate($colocation);

            DB::commit();

            return redirect()->route('expenses.index', $colocation)
                ->with('success', 'Depense ajoutee avec succes.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'ajout de la depense.')
                ->withInput();
        }
    }

    public function markPaid(Request $request, Colocation $colocation)
    {
        $this->ensureMember($colocation);

        $validated = $request->validate([
            'from_user_id' => [
                'required',
                'integer',
                $this->activeMemberRule($colocation),
            ],
            'to_user_id' => [
                'required',
                'integer',
                $this->activeMemberRule($colocation),
            ],
            'amount' => 'required|numeric|min:0.01',
        ]);

        if ((int) $validated['from_user_id'] === (int) $validated['to_user_id']) {
            return redirect()->back()->with('error', 'Paiement invalide.');
        }

        BalanceCalculator::recalculate($colocation);
        $settlements = collect(BalanceCalculator::getSettlements($colocation));

        $settlement = $settlements->first(function (array $item) use ($validated): bool {
            return (int) $item['from_id'] === (int) $validated['from_user_id']
                && (int) $item['to_id'] === (int) $validated['to_user_id'];
        });

        if (!$settlement) {
            return redirect()->back()->with('error', 'Ce remboursement n\'est plus valide.');
        }

        $maxAmount = (float) $settlement['amount'];
        $requestedAmount = round((float) $validated['amount'], 2);

        if ($requestedAmount > $maxAmount + 0.01) {
            return redirect()->back()->with('error', 'Le montant depasse la dette restante.');
        }

        DB::beginTransaction();
        try {
            Payment::create([
                'colocation_id' => $colocation->id,
                'from_user_id' => $validated['from_user_id'],
                'to_user_id' => $validated['to_user_id'],
                'amount' => $requestedAmount,
                'paid_at' => now(),
                'created_by' => Auth::id(),
            ]);

            BalanceCalculator::recalculate($colocation);

            DB::commit();

            return redirect()->route('expenses.index', $colocation)
                ->with('success', 'Paiement enregistre avec succes.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de l\'enregistrement du paiement.');
        }
    }

    public function destroy(Colocation $colocation, Expense $expense)
    {
        $this->ensureMember($colocation);

        if ($expense->colocation_id !== $colocation->id) {
            abort(404);
        }

        DB::beginTransaction();
        try {
            $expense->delete();
            BalanceCalculator::recalculate($colocation);

            DB::commit();

            return redirect()->back()->with('success', 'Depense supprimee.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de la suppression.');
        }
    }

    private function ensureMember(Colocation $colocation): void
    {
        if (!$colocation->hasMember(Auth::user())) {
            abort(403);
        }
    }

    private function activeMemberRule(Colocation $colocation): Exists
    {
        return Rule::exists('memberships', 'user_id')
            ->where(fn($query) => $query
                ->where('colocation_id', $colocation->id)
                ->whereNull('left_at'));
    }
}
