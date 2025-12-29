<?php

namespace App\Http\Controllers;

use App\Models\CashTransfer;
use App\Models\CashBank;
use App\Models\AccountTransaction;
use App\Models\CommonModel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class CashTransferController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $transfers = CashTransfer::with(['fromAccount', 'toAccount'])
                ->latest()
                ->get();

            return response()->json($transfers);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch cash transfers',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
            'from_account' => 'required|exists:cash_banks,id',
            'to_account' => 'required|exists:cash_banks,id|different:from_account',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:1000'
        ]);

        DB::beginTransaction();

        try {

            $transfer = new CashTransfer;
            $transfer->date = $request->date;
            $transfer->from_account = $request->from_account;
            $transfer->to_account = $request->to_account;
            $transfer->amount = $request->amount;
            $transfer->description = $request->description;
            $transfer->save();

            $description = $request->description ?? 'Cash Transfer';

            $transation = new AccountTransaction;
            $transation->transaction_type = 'Cash Transfer';
            $transation->voucher_type = 'CREDIT';
            $transation->date = $request->date;
            $transation->account_id = $request->to_account;
            $transation->reference_id = $transfer->id;
            $transation->description = $description . ' - Received';
            $transation->amount = $request->amount;
            $transation->validity = 1;
            $transation->save();

            $transation = new AccountTransaction;
            $transation->transaction_type = 'Cash Transfer';
            $transation->voucher_type = 'DEBIT';
            $transation->date = $request->date;
            $transation->account_id = $request->from_account;
            $transation->reference_id = $transfer->id;
            $transation->description = $description . ' - Sent';
            $transation->amount = $request->amount;
            $transation->validity = 1;
            $transation->save();

            CommonModel::updateAccountCurrentBalance();

            DB::commit();

            return response()->json([
                'message' => 'Cash transfer completed successfully',
                'transfer' => $transfer
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Cash Transfer Store Error: ' . $e->getMessage());
            \Log::error('Stack Trace: ' . $e->getTraceAsString());

            return response()->json([
                'error' => 'Failed to create cash transfer',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $transfer = CashTransfer::with(['fromAccount', 'toAccount'])->findOrFail($id);

            return response()->json($transfer);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Cash transfer not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch cash transfer',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'date' => 'sometimes|required|date',
            'from_account' => 'sometimes|required|exists:cash_banks,id|different:to_account',
            'to_account' => 'sometimes|required|exists:cash_banks,id|different:from_account',
            'amount' => 'sometimes|required|numeric|min:0.01',
            'description' => 'nullable|string|max:1000'
        ]);

        DB::beginTransaction();

        try {
            $transfer = CashTransfer::with(['fromAccount', 'toAccount'])->findOrFail($id);

            $newFromAccountId = $request->from_account ?? $transfer->from_account;
            $newToAccountId = $request->to_account ?? $transfer->to_account;
            $newAmount = $request->amount ?? $transfer->amount;

            $transfer->date = $request->date ?? $transfer->date;
            $transfer->from_account = $newFromAccountId;
            $transfer->to_account = $newToAccountId;
            $transfer->amount = $newAmount;
            $transfer->description = $request->description ?? $transfer->description;
            $transfer->save();

            $description = $request->description ?? $transfer->description ?? 'Cash Transfer';
            $date = $request->date ?? $transfer->date;

            $creditTransaction = AccountTransaction::where('reference_id', $transfer->id)
                ->where('transaction_type', 'Cash Transfer')
                ->where('voucher_type', 'CREDIT')
                ->first();

            if ($creditTransaction) {
                $creditTransaction->update([
                    'date' => $date,
                    'account_id' => $newToAccountId,
                    'description' => $description . ' - Received',
                    'amount' => $newAmount,
                    'updated_by' => auth()->id(),
                ]);
            }

            $debitTransaction = AccountTransaction::where('reference_id', $transfer->id)
                ->where('transaction_type', 'Cash Transfer')
                ->where('voucher_type', 'DEBIT')
                ->first();

            if ($debitTransaction) {
                $debitTransaction->update([
                    'date' => $date,
                    'account_id' => $newFromAccountId,
                    'description' => $description . ' - Sent',
                    'amount' => $newAmount,
                    'updated_by' => auth()->id(),
                ]);
            }

            CommonModel::updateAccountCurrentBalance();

            DB::commit();

            return response()->json([
                'message' => 'Cash transfer updated successfully',
                'transfer' => $transfer
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Cash transfer not found'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Cash Transfer Update Error: ' . $e->getMessage());
            \Log::error('Stack Trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'Failed to update cash transfer',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $transfer = CashTransfer::findOrFail($id);

            AccountTransaction::invalidateTransactions($transfer->id);

            $transfer->delete();

            CommonModel::updateAccountCurrentBalance();

            DB::commit();

            return response()->json([
                'message' => 'Cash transfer deleted successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Cash transfer not found'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Cash Transfer Delete Error: ' . $e->getMessage());
            \Log::error('Stack Trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'Failed to delete cash transfer',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function statistics(): JsonResponse
    {
        try {
            $query = CashTransfer::query();
            $stats = $query->selectRaw('
                COUNT(*) as total_transfers,
                SUM(amount) as total_amount,
                SUM(CASE WHEN DATE(date) = CURDATE() THEN 1 ELSE 0 END) as today_transfers,
                SUM(CASE WHEN DATE(date) = CURDATE() THEN amount ELSE 0 END) as today_amount
            ')->first();
            
            $monthlyQuery = CashTransfer::selectRaw('
                MONTH(date) as month,
                COUNT(*) as count,
                SUM(amount) as total_amount
            ')
            ->whereYear('date', date('Y'))
            ->groupBy(DB::raw('MONTH(date)'))
            ->orderBy('month')
            ->get();

            return response()->json([
                'total_transfers' => (int) $stats->total_transfers,
                'total_amount' => (float) $stats->total_amount,
                'today_transfers' => (int) $stats->today_transfers,
                'today_amount' => (float) $stats->today_amount,
                'monthly_summary' => $monthlyQuery
            ]);
        } catch (\Exception $e) {
            \Log::error('CashTransfer Statistics Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch statistics',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getByDateRange(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date'
            ]);

            $transfers = CashTransfer::with(['fromAccount', 'toAccount'])
                ->whereBetween('date', [$request->start_date, $request->end_date])
                ->latest()
                ->get();

            $totalAmount = $transfers->sum('amount');

            return response()->json([
                'transfers' => $transfers,
                'total_amount' => (float) $totalAmount,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'count' => $transfers->count()
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch transfers',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getByAccount(string $accountId): JsonResponse
    {
        try {
            $account = CashBank::findOrFail($accountId);

            $transfers = CashTransfer::with(['fromAccount', 'toAccount'])
                ->where('from_account', $accountId)
                ->orWhere('to_account', $accountId)
                ->latest()
                ->get();

            $totalSent = $transfers->where('from_account', $accountId)->sum('amount');
            $totalReceived = $transfers->where('to_account', $accountId)->sum('amount');

            return response()->json([
                'account' => $account,
                'transfers' => $transfers,
                'summary' => [
                    'total_sent' => (float) $totalSent,
                    'total_received' => (float) $totalReceived,
                    'net_flow' => (float) ($totalReceived - $totalSent),
                    'total_transfers' => $transfers->count()
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Account not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch account transfers',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}