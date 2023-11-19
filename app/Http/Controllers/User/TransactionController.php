<?php

namespace App\Http\Controllers\User;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
{
    public function list(Request $request)
    {
        $transactions = Transaction::where('user_id', auth('api.user')->id())->with('payments')->get();
        if (count($transactions) == 0) {
            return response()->json(['message' => 'Not have transactions.'], 404);
        }
        return response()->json(compact('transactions'));
    }

    public function view($id)
    {
        $transaction = Transaction::where('user_id', auth('api.user')->id())
            ->where('id', $id)
            ->with('payments')
            ->first();
        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found.'], 404);
        }
        return response()->json(compact('transaction'));
    }
}
