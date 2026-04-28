<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('events')->latest()->paginate(10);
        $totalCategories = Category::count();
        $totalEventsWithCategories = Category::has('events')->count();
        
        return view('categories.index', compact('categories', 'totalCategories', 'totalEventsWithCategories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(StoreCategoryRequest $request)
    {
        Category::create($request->validated());

        return redirect()->route('categories.index')
            ->with('success', 'Category berhasil dibuat');
    }

    public function show(Category $category)
    {
        $category->loadCount('events');
        $category->load('events');
        
        return view('categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return redirect()->route('categories.index')
            ->with('success', 'Category berhasil diupdate');
    }

    public function destroy(Category $category)
    {
        // Check if category has events
        if ($category->events()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Tidak dapat menghapus kategori yang masih memiliki event');
        }
        
        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Category berhasil dihapus');
    }
}