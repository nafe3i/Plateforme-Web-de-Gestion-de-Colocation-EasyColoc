<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $activeColocation = $user->activeColocation();

        $categories = Category::orderBy('name')->get();
        $canManage = $this->canManage($user);

        return view('categories.index', compact('categories', 'canManage', 'activeColocation'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeManagement();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Categorie creee.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $this->authorizeManagement();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($category->id),
            ],
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Categorie mise a jour.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->authorizeManagement();

        if ($category->expenses()->exists()) {
            return redirect()->route('categories.index')
                ->with('error', 'Categorie utilisee dans des depenses, suppression impossible.');
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Categorie supprimee.');
    }

    private function authorizeManagement(): void
    {
        if (!$this->canManage(Auth::user())) {
            abort(403, 'Seul un owner ou admin global peut gerer les categories.');
        }
    }

    private function canManage(User $user): bool
    {
        if ($user->hasRole('adminGlobal')) {
            return true;
        }

        $activeColocation = $user->activeColocation();

        return $activeColocation && $activeColocation->isOwner($user);
    }
}
