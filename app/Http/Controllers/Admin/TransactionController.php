<?php

namespace App\Http\Controllers\Admin;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\TransactionPayment;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateTransactionRequest;
use App\Http\Requests\Admin\CrateTransactionPaymentRequest;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function list(Request $request)
    {
        $transactions = Transaction::with('payments')->get();
        return response()->json(compact('transactions'));
    }

    public function view($id)
    {
        $transaction = Transaction::with('payments')->find($id);
        if ($transaction) {
            return response()->json(compact('transaction'));
        }
        return response()->json(['message' => 'Transaction not found.'], '404');
    }

    public function create(CreateTransactionRequest $request)
    {
        $inputs = $request->validated();
        $inputs['admin_id'] = auth('api.admin')->id();
        $inputs['unpaid'] = $request->amount;
        $transaction = Transaction::create($inputs);
        return response()->json([
            'message' => 'Created Transaction Successfully.',
            'transaction' => $transaction
        ]);
    }

    public function payment(CrateTransactionPaymentRequest $request)
    {
        $inputs = $request->validated();
        $transaction = Transaction::find($request->transaction_id);
        $amount = floatval($request->amount);
        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found.'], 404);
        } elseif ($transaction->unpaid < 1) {
            return response()->json(['message' => 'Transaction is paid.'], 401);
        } elseif ($transaction->unpaid >= $amount) {
            DB::beginTransaction();
            $transaction->update([
                'unpaid' => $transaction->unpaid - $amount,
                'paid'   => $transaction->paid + $amount,
            ]);
            $inputs['admin_id'] = auth('api.admin')->id();
            $inputs['paid_at'] = now();
            $transaction = TransactionPayment::create($inputs);
            DB::commit();
            return response()->json([
                'message'     => 'Created Transaction Payment Successfully.',
                'transaction' => $transaction
            ]);
        } elseif ($transaction->unpaid < $amount) {
            return response()->json([
                'message' => 'This amount grater than transaction amount.'
            ], 401);
        }
    }

    public function report(Request $request)
    {
        $request->validate([
            'start_date' => 'required',
            'end_date' => 'required|after:start_date'
        ]);

        $start_date = Carbon::parse($request->start_date)->format('Y-m-d');
        $end_date = Carbon::parse($request->end_date)->format('Y-m-d');
        $transactions = DB::table('transactions')
            ->select('*', DB::raw("DATE_FORMAT(created_at, '%m-%Y') new_date"),  DB::raw('YEAR(created_at) year, MONTH(created_at) month'))
            ->where('created_at', '>=', $start_date)
            ->where('created_at', '<=', $end_date)
            ->get()
            ->groupBy('new_date');
        $result = [];

        foreach ($transactions as $key => $group) {
            $result[] = (object)[
                'month'         => $group->first()->month,
                'year'          => $group->first()->year,
                'paid'          => $group->sum('paid'),
                'outstanding'   => $group->where('due_at', '>=', now())->sum('unpaid'),
                'overdue'       => $group->where('due_at', '<', now())->sum('unpaid'),
            ];
        }

        return response()->json($result);
    }
}
