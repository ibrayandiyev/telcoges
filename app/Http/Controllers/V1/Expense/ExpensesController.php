<?php

namespace Crater\Http\Controllers\V1\Expense;

use Crater\Models\Expense;
use Illuminate\Http\Request;
use Crater\Http\Requests\ExpenseRequest;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\DeleteExpensesRequest;

class ExpensesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $limit = $request->has('limit') ? $request->limit : 10;

        $expenses = Expense::with(['items', 'user', 'taxes', 'category', 'creator', 'fields'])
            ->join('users', 'users.id', '=', 'expenses.user_id')
            ->join('expense_categories', 'expense_categories.id', '=', 'expenses.expense_category_id')
            ->applyFilters($request->only([
                'status',
                'expense_category_id',
                'user_id',
                'doc_num',
                'expense_id',
                'search',
                'from_date',
                'to_date',
                'orderByField',
                'orderBy'
            ]))
            ->whereCompany($request->header('company'))
            ->select('expenses.*', 'expense_categories.name', 'users.name as user_name')
            ->paginateData($limit);

        return response()->json([
            'expenses' => $expenses,
            'expenseTotalCount' => Expense::count()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ExpenseRequest $request)
    {
        $expense = Expense::createExpense($request);

        return response()->json([
            'expense' => $expense,
            'success' => true
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\Expense $expense
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Expense $expense)
    {
        // $expense->load('creator', 'fields.customField');

        $expense->load([
            'items',
            'items.taxes',
            'user',
            'taxes.taxType',
            'fields.customField'
        ]);

        return response()->json([
            'expense' => $expense
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Crater\Models\Expense $expense
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ExpenseRequest $request, Expense $expense)
    {
        $expense->updateExpense($request);

        return response()->json([
            'expense' => $expense,
            'success' => true
        ]);
    }

    public function delete(DeleteExpensesRequest $request)
    {
        Expense::destroy($request->ids);

        return response()->json([
            'success' => true
        ]);
    }
}
