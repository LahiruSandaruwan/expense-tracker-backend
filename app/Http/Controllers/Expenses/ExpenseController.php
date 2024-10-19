<?php

namespace App\Http\Controllers\Expenses;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the expenses.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Get the authenticated user's ID
        $userId = Auth::id();

        // Fetch expenses directly using the Expense model and filter by the authenticated user's ID
        $expenses = Expense::where('user_id', $userId)
            ->orderBy('date', 'desc')
            ->get();

        return response()->json($expenses);
    }

    /**
     * Store a newly created expense in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'category' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Add the authenticated user's ID to the validated data
        $validatedData['user_id'] = Auth::id();

        // Check if an expense with the same title already exists for the user
        $existingExpense = Expense::where('user_id', $validatedData['user_id'])
            ->where('title', $validatedData['title'])
            ->first();

        if ($existingExpense) {
            // If a duplicate title is found, return an error response
            return response()->json(['error' => 'An expense with this title already exists.'], 422);
        }

        // Create a new expense with the validated data
        $expense = Expense::create($validatedData);

        return response()->json($expense, 201);
    }

    /**
     * Display the specified expense.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $userId = Auth::id();

        // Find the expense that belongs to the authenticated user
        $expense = Expense::where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        return response()->json($expense);
    }

    /**
     * Update the specified expense in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $userId = Auth::id();

        // Find the expense that belongs to the authenticated user
        $expense = Expense::where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        // Validate the request data
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'category' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Update the expense with validated data
        $expense->update($validatedData);

        return response()->json($expense);
    }

    /**
     * Remove the specified expense from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $userId = Auth::id();

        // Find the expense that belongs to the authenticated user
        $expense = Expense::where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        // Delete the expense
        $expense->delete();

        return response()->json(['message' => 'Expense deleted successfully']);
    }
}
