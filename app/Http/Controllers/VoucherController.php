<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Models\CashBank;
use App\Models\Subsidiary;
use App\Models\AccountHead;
use App\Models\AccountTransaction;
use App\Models\CommonModel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class VoucherController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $vouchers = Voucher::with(['account', 'subsidiary', 'head'])
                ->orderBy('created_at', 'desc')
                ->get();
                
            return response()->json($vouchers);
        } catch (\Exception $e) {
            \Log::error('Error fetching vouchers: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch vouchers',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
            'voucher_type' => 'required|in:DEBIT,CREDIT',
            'account_id' => 'required|exists:cash_banks,id',
            'subsidiaries_id' => 'nullable|exists:subsidiaries,id',
            'head_id' => 'nullable|exists:account_heads,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:1000',
            'module_name' => 'nullable|string|max:255',
            'ref_module_id' => 'nullable|integer',
        ]);

        DB::beginTransaction();

        try {
            $voucher = new Voucher();
            $voucher->date = $request->date;
            $voucher->voucher_type = $request->voucher_type;
            $voucher->account_id = $request->account_id;
            $voucher->subsidiaries_id = $request->subsidiaries_id;
            $voucher->head_id = $request->head_id;
            $voucher->amount = $request->amount;
            $voucher->description = $request->description;
            $voucher->module_name = $request->module_name;
            $voucher->ref_module_id = $request->ref_module_id;
            $voucher->save();

            $transaction = new AccountTransaction;
            $transaction->transaction_type = 'Voucher';
            $transaction->voucher_type = $request->voucher_type;
            $transaction->date = $request->date;
            $transaction->account_id = $request->account_id;
            $transaction->reference_id = $voucher->id;
            $transaction->description = $request->description ?? 'Voucher Entry';
            $transaction->amount = $request->amount;
            $transaction->validity = 1;
            $transaction->save();

            CommonModel::updateAccountCurrentBalance();

            DB::commit();

            return response()->json([
                'message' => 'Voucher created successfully',
                'voucher' => $voucher->load(['account', 'subsidiary', 'head'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Voucher Store Error: ' . $e->getMessage());
            \Log::error('Stack Trace: ' . $e->getTraceAsString());

            return response()->json([
                'error' => 'Failed to create voucher',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $voucher = Voucher::with(['account', 'subsidiary', 'head'])->findOrFail($id);
            return response()->json($voucher);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Voucher not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch voucher',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'date' => 'sometimes|required|date',
            'voucher_type' => 'sometimes|required|in:DEBIT,CREDIT',
            'account_id' => 'sometimes|required|exists:cash_banks,id',
            'subsidiaries_id' => 'nullable|exists:subsidiaries,id',
            'head_id' => 'nullable|exists:account_heads,id',
            'amount' => 'sometimes|required|numeric|min:0.01',
            'description' => 'nullable|string|max:1000',
            'module_name' => 'nullable|string|max:255',
            'ref_module_id' => 'nullable|integer',
        ]);

        DB::beginTransaction();

        try {
            $voucher = Voucher::findOrFail($id);
            
            $voucher->date = $request->date ?? $voucher->date;
            $voucher->voucher_type = $request->voucher_type ?? $voucher->voucher_type;
            $voucher->account_id = $request->account_id ?? $voucher->account_id;
            $voucher->amount = $request->amount ?? $voucher->amount;
            $voucher->description = $request->description ?? $voucher->description;
            $voucher->module_name = $request->module_name ?? $voucher->module_name;
            $voucher->ref_module_id = $request->ref_module_id ?? $voucher->ref_module_id;

            if ($request->exists('subsidiaries_id')) {
                $voucher->subsidiaries_id = $request->subsidiaries_id ?: null;
            }
            if ($request->exists('head_id')) {
                $voucher->head_id = $request->head_id ?: null;
            }
            
            $voucher->save();

            $transaction = AccountTransaction::where('reference_id', $voucher->id)
                ->whereIn('transaction_type', ['Voucher', 'Order Commission', 'Servicing Charge'])
                ->first();

            if ($transaction) {
                $transactionType = 'Voucher';
                if ($voucher->module_name === 'order_commission') {
                    $transactionType = 'Order Commission';
                } elseif ($voucher->module_name === 'servicing') {
                    $transactionType = 'Servicing Charge';
                }

                $transaction->update([
                    'transaction_type' => $transactionType,
                    'voucher_type' => $voucher->voucher_type,
                    'date' => $voucher->date,
                    'account_id' => $voucher->account_id,
                    'description' => $voucher->description ?? "Voucher Entry ($transactionType)",
                    'amount' => $voucher->amount,
                    'updated_by' => auth()->id(),
                ]);
            } else {
                $transactionType = 'Voucher';
                if ($voucher->module_name === 'SALARY_ADVANCE') {
                    $transactionType = 'SALARY_ADVANCE';
                } elseif ($voucher->module_name === 'SALARY') {
                    $transactionType = 'SALARY_PAYMENT';
                }

                $transaction = new AccountTransaction;
                $transaction->transaction_type = $transactionType;
                $transaction->voucher_type = $voucher->voucher_type;
                $transaction->date = $voucher->date;
                $transaction->account_id = $voucher->account_id;
                $transaction->reference_id = $voucher->id;
                $transaction->description = $voucher->description ?? "Voucher Entry ($transactionType)";
                $transaction->amount = $voucher->amount;
                $transaction->validity = 1;
                $transaction->created_by = auth()->id();
                $transaction->save();
            }

            CommonModel::updateAccountCurrentBalance();

            DB::commit();

            return response()->json([
                'message' => 'Voucher updated successfully',
                'voucher' => $voucher->load(['account', 'subsidiary', 'head'])
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Voucher not found'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Voucher Update Error: ' . $e->getMessage());
            \Log::error('Stack Trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'Failed to update voucher',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function destroy(string $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $voucher = Voucher::findOrFail($id);

            AccountTransaction::where('reference_id', $voucher->id)
            ->whereIn('transaction_type', ['Voucher', 'Fee Payment', 'SALARY_ADVANCE', 'SALARY_PAYMENT'])
            ->update(['validity' => 0]);

            // if ($voucher->module_name === 'order_commission') {
            //     $this->adjustAccountBalanceForOrderCommission(
            //         $voucher->account_id, 
            //         $voucher->amount, 
            //         $voucher->voucher_type, 
            //         'reverse'
            //     );
            // }

            // AccountTransaction::invalidateTransactions($voucher->id, 'Voucher');

            $voucher->delete();

            CommonModel::updateAccountCurrentBalance();

            DB::commit();

            return response()->json([
                'message' => 'Voucher deleted successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Voucher not found'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Voucher Delete Error: ' . $e->getMessage());
            \Log::error('Stack Trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'Failed to delete voucher',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getDropdownData(): JsonResponse
    {
        try {
            \Log::info('Fetching dropdown data started');

            $vouchers = Voucher::with(['account', 'subsidiary', 'head'])
                ->orderBy('created_at', 'desc')
                ->get();

            $accounts = CashBank::select('id', 'account_name as name', 'current_balance as balance')
                ->orderBy('account_name')
                ->get()
                ->map(function($account) {
                    return [
                        'id' => $account->id,
                        'name' => $account->name,
                        'balance' => $account->balance,
                        'account_number' => '' 
                    ];
                });

            $subsidiaries = Subsidiary::select('id', 'name')
                ->orderBy('name')
                ->get()
                ->map(function($subsidiary) {
                    return [
                        'id' => $subsidiary->id,
                        'name' => $subsidiary->name,
                        'code' => '' 
                    ];
                });

            $heads = AccountHead::select('id', 'head_name as name', 'description')
                ->orderBy('head_name')
                ->get()
                ->map(function($head) {
                    return [
                        'id' => $head->id,
                        'name' => $head->name,
                        'code' => '', 
                        'description' => $head->description
                    ];
                });

            \Log::info('Dropdown data fetched successfully');
            \Log::info('Vouchers count: ' . $vouchers->count());
            \Log::info('Accounts count: ' . $accounts->count());
            \Log::info('Subsidiaries count: ' . $subsidiaries->count());
            \Log::info('Heads count: ' . $heads->count());

            return response()->json([
                'vouchers' => $vouchers, 
                'accounts' => $accounts,
                'subsidiaries' => $subsidiaries,
                'heads' => $heads
            ]);

        } catch (\Exception $e) {
            \Log::error('Dropdown data error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'Failed to fetch dropdown data',
                'message' => $e->getMessage()
            ], 500); 
        }
    }

    public function statistics(): JsonResponse
    {
        try {
            $vouchers = Voucher::all();
            $today = Carbon::today()->toDateString();
            
            $totalVouchers = $vouchers->count();
            $totalAmount = $vouchers->sum('amount');
            
            $todayVouchers = $vouchers->filter(function($voucher) use ($today) {
                return $voucher->date->toDateString() === $today;
            });
            
            $todayCount = $todayVouchers->count();
            $todayAmount = $todayVouchers->sum('amount');
            
            $debitAmount = $vouchers->where('voucher_type', 'DEBIT')->sum('amount');
            $creditAmount = $vouchers->where('voucher_type', 'CREDIT')->sum('amount');
            
            $currentYear = date('Y');
            $monthlySummary = $vouchers
                ->filter(function($voucher) use ($currentYear) {
                    return $voucher->date->year == $currentYear;
                })
                ->groupBy(function($voucher) {
                    return $voucher->date->month; 
                })
                ->map(function($monthVouchers, $month) {
                    return [
                        'month' => (int) $month,
                        'count' => $monthVouchers->count(),
                        'total_amount' => (float) $monthVouchers->sum('amount')
                    ];
                })
                ->values()
                ->sortBy('month');

            return response()->json([
                'total_vouchers' => $totalVouchers,
                'total_amount' => (float) $totalAmount,
                'today_vouchers' => $todayCount,
                'today_amount' => (float) $todayAmount,
                'debit_amount' => (float) $debitAmount,
                'credit_amount' => (float) $creditAmount,
                'monthly_summary' => $monthlySummary
            ]);
        } catch (\Exception $e) {
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

            $vouchers = Voucher::with(['account', 'subsidiary', 'head'])
                ->whereBetween('date', [$request->start_date, $request->end_date])
                ->latest()
                ->get();

            $totalAmount = $vouchers->sum('amount');
            $debitAmount = $vouchers->where('voucher_type', 'DEBIT')->sum('amount');
            $creditAmount = $vouchers->where('voucher_type', 'CREDIT')->sum('amount');

            return response()->json([
                'vouchers' => $vouchers,
                'total_amount' => (float) $totalAmount,
                'debit_amount' => (float) $debitAmount,
                'credit_amount' => (float) $creditAmount,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'count' => $vouchers->count()
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch vouchers',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getByAccount(string $accountId): JsonResponse
    {
        try {
            $account = CashBank::findOrFail($accountId);

            $vouchers = Voucher::with(['account', 'subsidiary', 'head'])
                ->where('account_id', $accountId)
                ->latest()
                ->get();

            $totalDebit = $vouchers->where('voucher_type', 'DEBIT')->sum('amount');
            $totalCredit = $vouchers->where('voucher_type', 'CREDIT')->sum('amount');

            return response()->json([
                'account' => $account,
                'vouchers' => $vouchers,
                'summary' => [
                    'total_debit' => (float) $totalDebit,
                    'total_credit' => (float) $totalCredit,
                    'net_flow' => (float) ($totalCredit - $totalDebit),
                    'total_vouchers' => $vouchers->count()
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Account not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch account vouchers',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}