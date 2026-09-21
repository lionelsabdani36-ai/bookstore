<?php
namespace App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index() { return response()->json(['status' => 'success', 'data' => Category::all()]); }
    public function store(Request $request) {
        $validated = $request->validate(['name' => 'required|string|unique:categories']);
        return response()->json(['status' => 'success', 'data' => Category::create($validated)]);
    }
    public function show(Category $category) { return response()->json(['status' => 'success', 'data' => $category]); }
    public function update(Request $request, Category $category) {
        $validated = $request->validate(['name' => 'required|string|unique:categories,name,' . $category->id]);
        $category->update($validated);
        return response()->json(['status' => 'success', 'data' => $category]);
    }
    public function destroy(Category $category) {
        $category->delete();
        return response()->json(['status' => 'success', 'message' => 'Deleted']);
    }
}