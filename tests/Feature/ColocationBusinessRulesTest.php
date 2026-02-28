<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Colocation;
use App\Models\Expense;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\User;
use App\Services\BalanceCalculator;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ColocationBusinessRulesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_mark_paid_creates_payment_and_updates_balances(): void
    {
        [$owner, $member, $colocation] = $this->createBasicColocation();

        $category = Category::create(['name' => 'Courses']);

        Expense::create([
            'colocation_id' => $colocation->id,
            'paid_by' => $owner->id,
            'category_id' => $category->id,
            'title' => 'Supermarche',
            'amount' => 100,
            'date' => now()->toDateString(),
        ]);

        BalanceCalculator::recalculate($colocation);

        $response = $this->actingAs($member)->post(route('settlements.pay', $colocation), [
            'from_user_id' => $member->id,
            'to_user_id' => $owner->id,
            'amount' => 20,
        ]);

        $response->assertRedirect(route('expenses.index', $colocation));

        $this->assertDatabaseHas('payments', [
            'colocation_id' => $colocation->id,
            'from_user_id' => $member->id,
            'to_user_id' => $owner->id,
            'amount' => 20.00,
        ]);

        $ownerBalance = (float) Membership::where('colocation_id', $colocation->id)
            ->where('user_id', $owner->id)
            ->whereNull('left_at')
            ->value('balance');
        $memberBalance = (float) Membership::where('colocation_id', $colocation->id)
            ->where('user_id', $member->id)
            ->whereNull('left_at')
            ->value('balance');

        $this->assertEquals(-30.0, $ownerBalance);
        $this->assertEquals(30.0, $memberBalance);
        $this->assertEquals(1, Payment::count());
    }

    public function test_leave_with_debt_redistributes_balance_and_updates_reputation(): void
    {
        $owner = User::factory()->create(['reputation' => 0]);
        $leavingMember = User::factory()->create(['reputation' => 0]);
        $thirdMember = User::factory()->create(['reputation' => 0]);

        $colocation = Colocation::create([
            'name' => 'Maison Test',
            'description' => 'Test',
            'owner_id' => $owner->id,
            'status' => 'active',
        ]);

        Membership::create([
            'user_id' => $owner->id,
            'colocation_id' => $colocation->id,
            'role' => 'owner',
            'balance' => 0,
            'manual_adjustment' => 0,
            'joined_at' => now()->subDay(),
        ]);

        Membership::create([
            'user_id' => $leavingMember->id,
            'colocation_id' => $colocation->id,
            'role' => 'member',
            'balance' => 0,
            'manual_adjustment' => 0,
            'joined_at' => now()->subDay(),
        ]);

        Membership::create([
            'user_id' => $thirdMember->id,
            'colocation_id' => $colocation->id,
            'role' => 'member',
            'balance' => 0,
            'manual_adjustment' => 0,
            'joined_at' => now()->subDay(),
        ]);

        $category = Category::create(['name' => 'Loyer']);

        Expense::create([
            'colocation_id' => $colocation->id,
            'paid_by' => $owner->id,
            'category_id' => $category->id,
            'title' => 'Loyer Mars',
            'amount' => 90,
            'date' => now()->toDateString(),
        ]);

        BalanceCalculator::recalculate($colocation);

        $response = $this->actingAs($leavingMember)->post(route('colocations.leave', $colocation));
        $response->assertRedirect(route('colocations.index'));

        $leavingMember->refresh();
        $this->assertEquals(-1, $leavingMember->reputation);

        $this->assertDatabaseHas('memberships', [
            'colocation_id' => $colocation->id,
            'user_id' => $leavingMember->id,
        ]);

        $leftAt = Membership::where('colocation_id', $colocation->id)
            ->where('user_id', $leavingMember->id)
            ->value('left_at');
        $this->assertNotNull($leftAt);

        $ownerBalance = (float) Membership::where('colocation_id', $colocation->id)
            ->where('user_id', $owner->id)
            ->whereNull('left_at')
            ->value('balance');
        $thirdBalance = (float) Membership::where('colocation_id', $colocation->id)
            ->where('user_id', $thirdMember->id)
            ->whereNull('left_at')
            ->value('balance');

        $this->assertEquals(-45.0, $ownerBalance);
        $this->assertEquals(45.0, $thirdBalance);
    }

    private function createBasicColocation(): array
    {
        $owner = User::factory()->create(['reputation' => 0]);
        $member = User::factory()->create(['reputation' => 0]);

        $colocation = Colocation::create([
            'name' => 'Appartement Test',
            'description' => 'Test',
            'owner_id' => $owner->id,
            'status' => 'active',
        ]);

        Membership::create([
            'user_id' => $owner->id,
            'colocation_id' => $colocation->id,
            'role' => 'owner',
            'balance' => 0,
            'manual_adjustment' => 0,
            'joined_at' => now()->subDay(),
        ]);

        Membership::create([
            'user_id' => $member->id,
            'colocation_id' => $colocation->id,
            'role' => 'member',
            'balance' => 0,
            'manual_adjustment' => 0,
            'joined_at' => now()->subDay(),
        ]);

        return [$owner, $member, $colocation];
    }
}
