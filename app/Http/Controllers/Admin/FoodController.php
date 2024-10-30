<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Models\Branch;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\FoodRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class FoodController extends Controller
{
    public function index(Request $request, Branch $branch)
    {
        $foods = Food::with(['category', 'extraOptions'])
            ->where('branch_id', $branch->id)
            ->where('is_available', true)
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category_id');

        $categories = Category::whereHas('foods', function($query) use ($branch) {
            $query->where('branch_id', $branch->id)
                  ->where('is_available', true);
        })->get();

        return Inertia::render('Customer/FoodMenu', [
            'branch' => $branch,
            'categories' => $categories,
            'foods' => $foods,
            'orderType' => $request->query('type', 'collection')
        ]);
    }


    public function create()
    {
        return Inertia::render('Admin/Foods/Form', [
            'food' => null,
            'categories' => Category::select('id', 'name')->get(),
            'branches' => Branch::select('id', 'name')->get()
        ]);
    }

    public function store(FoodRequest $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();
            $extraOptions = $validated['extra_options'] ?? [];
            unset($validated['extra_options']);

            if ($request->hasFile('image')) {
                $validated['image_path'] = $request->file('image')->store('foods', 'public');
            }

            $food = Food::create($validated);

            // Create extra options
            foreach ($extraOptions as $option) {
                $food->extraOptions()->create([
                    'name' => $option['name'],
                    'price' => $option['price'],
                    'is_available' => $option['is_available'] ?? true,
                    'sort_order' => $option['sort_order'] ?? 0
                ]);
            }

            DB::commit();

            return redirect()->route('admin.foods.index')
                ->with('success', 'Food item created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($validated['image_path'])) {
                Storage::disk('public')->delete($validated['image_path']);
            }
            return back()->with('error', 'Error creating food item. Please try again.');
        }
    }

    public function edit(Food $food)
    {
        return Inertia::render('Admin/Foods/Form', [
            'food' => $food->load('extraOptions'),
            'categories' => Category::select('id', 'name')->get(),
            'branches' => Branch::select('id', 'name')->get()
        ]);
    }

    public function update(FoodRequest $request, Food $food)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();
            $extraOptions = $validated['extra_options'] ?? [];
            unset($validated['extra_options']);

            if ($request->hasFile('image')) {
                if ($food->image_path) {
                    Storage::disk('public')->delete($food->image_path);
                }
                $validated['image_path'] = $request->file('image')->store('foods', 'public');
            }

            $food->update($validated);

            // Update extra options
            $existingIds = [];
            foreach ($extraOptions as $option) {
                if (isset($option['id'])) {
                    // Update existing option
                    $food->extraOptions()->where('id', $option['id'])->update([
                        'name' => $option['name'],
                        'price' => $option['price'],
                        'is_available' => $option['is_available'] ?? true,
                        'sort_order' => $option['sort_order'] ?? 0
                    ]);
                    $existingIds[] = $option['id'];
                } else {
                    // Create new option
                    $newOption = $food->extraOptions()->create([
                        'name' => $option['name'],
                        'price' => $option['price'],
                        'is_available' => $option['is_available'] ?? true,
                        'sort_order' => $option['sort_order'] ?? 0
                    ]);
                    $existingIds[] = $newOption->id;
                }
            }

            // Delete removed options
            $food->extraOptions()->whereNotIn('id', $existingIds)->delete();

            DB::commit();

            return redirect()->route('admin.foods.index')
                ->with('success', 'Food item updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($validated['image_path'])) {
                Storage::disk('public')->delete($validated['image_path']);
            }
            return back()->with('error', 'Error updating food item. Please try again.');
        }
    }

    public function destroy(Food $food)
    {
        try {
            DB::beginTransaction();

            if ($food->image_path) {
                Storage::disk('public')->delete($food->image_path);
            }

            // Delete related extra options
            $food->extraOptions()->delete();
            $food->delete();

            DB::commit();

            return back()->with('success', 'Food item deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting food item. Please try again.');
        }
    }

    public function updateStatus(Food $food)
    {
        $food->update(['is_available' => !$food->is_available]);
        return back()->with('success', 'Food availability updated successfully.');
    }
}
