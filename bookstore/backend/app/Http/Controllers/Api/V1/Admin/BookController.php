<?php
namespace App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index() { return response()->json(['status' => 'success', 'data' => Book::with('category')->get()]); }
    public function store(Request $request) {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|unique:books',
            'publish_date' => 'nullable|date',
            'stock' => 'required|integer',
            'cost_price' => 'required|numeric',
            'sell_price' => 'required|numeric',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image',
        ]);
        $validated['profit'] = $validated['sell_price'] - $validated['cost_price'];
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('books', 'public');
        }
        return response()->json(['status' => 'success', 'data' => Book::create($validated)]);
    }
    public function show(Book $book) { return response()->json(['status' => 'success', 'data' => $book->load('category')]); }
    public function update(Request $request, Book $book) {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|unique:books,name,' . $book->id,
            'publish_date' => 'nullable|date',
            'stock' => 'required|integer',
            'cost_price' => 'required|numeric',
            'sell_price' => 'required|numeric',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image',
        ]);
        $validated['profit'] = $validated['sell_price'] - $validated['cost_price'];
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('books', 'public');
        }
        $book->update($validated);
        return response()->json(['status' => 'success', 'data' => $book]);
    }
    public function destroy(Book $book) {
        $book->delete();
        return response()->json(['status' => 'success', 'message' => 'Deleted']);
    }
}