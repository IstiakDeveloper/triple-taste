<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Models\Branch;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\FoodRequest;
use Inertia\Inertia;

class FoodController extends Controller
{
    public function index()
    {
        $foods = Food::with(['branch', 'category', 'extraOptions'])
            ->orderBy('sort_order')
            ->paginate(10);

        return Inertia::render('Admin/Foods/Index', [
            'foods' => [
                'data' => $foods->items(),
                'meta' => [
                    'current_page' => $foods->currentPage(),
                    'from' => $foods->firstItem(),
                    'last_page' => $foods->lastPage(),
                    'links' => $foods->linkCollection()->toArray(),
                    'path' => $foods->path(),
                    'per_page' => $foods->perPage(),
                    'to' => $foods->lastItem(),
                    'total' => $foods->total(),
                ],
            ],
            'flash' => [
                'success' => session('success'),
                'error' => session('error')
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Foods/Form', [
            'branches' => Branch::all(),
            'categories' => Category::all(),
        ]);
    }

    public function store(FoodRequest $request)
    {
        try {
            Food::create($request->validated());
            return redirect()->route('admin.foods.index')
                ->with('success', 'Food item created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error creating food item. Please try again.');
        }
    }

    public function edit(Food $food)
    {
        return Inertia::render('Admin/Foods/Form', [
            'food' => $food->load('extraOptions'),
            'branches' => Branch::all(),
            'categories' => Category::all(),
        ]);
    }

    public function update(FoodRequest $request, Food $food)
    {
        try {
            $food->update($request->validated());
            return redirect()->route('admin.foods.index')
                ->with('success', 'Food item updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating food item. Please try again.');
        }
    }

    public function destroy(Food $food)
    {
        try {
            if ($food->extraOptions()->exists()) {
                return back()->with('error', 'Cannot delete food with associated extra options.');
            }

            $food->delete();
            return back()->with('success', 'Food item deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting food item. Please try again.');
        }
    }

    public function toggleAvailability(Food $food)
    {
        try {
            $food->update(['is_available' => !$food->is_available]);
            return back()->with('success', 'Food availability updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating availability. Please try again.');
        }
    }
}
