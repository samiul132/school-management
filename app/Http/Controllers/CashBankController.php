<?php

namespace App\Http\Controllers;

use App\Models\CashBank;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;

class CashBankController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $cashBanks = CashBank::all();
            return response()->json($cashBanks);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch cash/bank accounts',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $schoolId = auth()->user()->school_id;
        
        $request->validate([
            'account_name' => [
                'required', 
                'string', 
                'max:255',
                Rule::unique('cash_banks')->where(function ($query) use ($schoolId) {
                    return $query->where('school_id', $schoolId);
                })
            ],
            'opening_balance' => 'required|numeric|min:-999999999.99|max:999999999.99',
            'current_balance' => 'sometimes|numeric',
            'description' => 'nullable|string',
        ]);
        
        try {
            $cashBank = new CashBank();
            $cashBank->account_name = $request->account_name;
            $cashBank->opening_balance = $request->opening_balance;
            $cashBank->current_balance = $request->current_balance ?? $request->opening_balance;
            $cashBank->description = $request->description;
            $cashBank->save();

            return response()->json([
                'message' => 'Cash/Bank account created successfully',
                'cash_bank' => $cashBank
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create cash/bank account',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $cashBank = CashBank::findOrFail($id);
            return response()->json($cashBank);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Cash/Bank account not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch cash/bank account',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $schoolId = auth()->user()->school_id;
        
        $request->validate([
            'account_name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('cash_banks')->where(function ($query) use ($schoolId, $id) {
                    return $query->where('school_id', $schoolId)
                                ->where('id', '!=', $id);
                })
            ],
            'opening_balance' => 'sometimes|required|numeric|min:-999999999.99|max:999999999.99',
            'current_balance' => 'sometimes|numeric',
            'description' => 'nullable|string',
        ]);

        try {
            $cashBank = CashBank::findOrFail($id);

            if ($request->has('opening_balance') && $request->opening_balance != $cashBank->opening_balance) {
                $balanceDifference = $request->opening_balance - $cashBank->opening_balance;
                if (!$request->has('current_balance')) {
                    $cashBank->current_balance = $cashBank->current_balance + $balanceDifference;
                }
            }

            if ($request->has('account_name')) {
                $cashBank->account_name = $request->account_name;
            }
            if ($request->has('opening_balance')) {
                $cashBank->opening_balance = $request->opening_balance;
            }
            if ($request->has('current_balance')) {
                $cashBank->current_balance = $request->current_balance;
            }
            if ($request->has('description')) {
                $cashBank->description = $request->description;
            }
            
            $cashBank->save();

            return response()->json([
                'message' => 'Cash/Bank account updated successfully',
                'cash_bank' => $cashBank
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Cash/Bank account not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update cash/bank account',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $cashBank = CashBank::findOrFail($id);
            $cashBank->delete();

            return response()->json([
                'message' => 'Cash/Bank account deleted successfully'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Cash/Bank account not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete cash/bank account',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function statistics(): JsonResponse
    {
        try {
            $totalAccounts = CashBank::count();
            $totalBalance = CashBank::sum('current_balance');
            $totalOpeningBalance = CashBank::sum('opening_balance');
            
            $positiveBalanceAccounts = CashBank::where('current_balance', '>', 0)->count();
            $negativeBalanceAccounts = CashBank::where('current_balance', '<', 0)->count();
            $zeroBalanceAccounts = CashBank::where('current_balance', 0)->count();

            return response()->json([
                'total_accounts' => $totalAccounts,
                'total_balance' => (float) $totalBalance,
                'total_opening_balance' => (float) $totalOpeningBalance,
                'balance_summary' => [
                    'positive_balance_accounts' => $positiveBalanceAccounts,
                    'negative_balance_accounts' => $negativeBalanceAccounts,
                    'zero_balance_accounts' => $zeroBalanceAccounts,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch statistics',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateBalance(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:DEBIT,CREDIT' 
        ]);

        try {
            $cashBank = CashBank::findOrFail($id);

            $currentBalance = $cashBank->current_balance;
            $amount = $request->amount;

            if ($request->type === 'CREDIT') {
                $newBalance = $currentBalance + $amount;
            } else {
                $newBalance = $currentBalance - $amount;
            }

            $cashBank->current_balance = $newBalance;
            $cashBank->save();

            return response()->json([
                'message' => 'Account balance updated successfully',
                'previous_balance' => $currentBalance,
                'new_balance' => $newBalance,
                'transaction_amount' => $amount,
                'transaction_type' => $request->type,
                'currency' => 'BDT'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Cash/Bank account not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update account balance',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getBalance(string $id): JsonResponse
    {
        try {
            $cashBank = CashBank::findOrFail($id);

            return response()->json([
                'account_id' => $cashBank->id,
                'account_name' => $cashBank->account_name,
                'current_balance' => $cashBank->current_balance,
                'opening_balance' => $cashBank->opening_balance,
                'currency' => 'BDT'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Cash/Bank account not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch account balance',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function bulkUpdateBalances(Request $request): JsonResponse
    {
        $request->validate([
            'transactions' => 'required|array',
            'transactions.*.account_id' => 'required|exists:cash_banks,id',
            'transactions.*.amount' => 'required|numeric|min:0.01',
            'transactions.*.type' => 'required|in:DEBIT,CREDIT'
        ]);

        try {
            $results = [];
            
            foreach ($request->transactions as $transaction) {
                $cashBank = CashBank::find($transaction['account_id']);
                $currentBalance = $cashBank->current_balance;
                $amount = $transaction['amount'];

                if ($transaction['type'] === 'CREDIT') {
                    $newBalance = $currentBalance + $amount;
                } else {
                    $newBalance = $currentBalance - $amount;
                }

                $cashBank->current_balance = $newBalance;
                $cashBank->save();

                $results[] = [
                    'account_id' => $cashBank->id,
                    'account_name' => $cashBank->account_name,
                    'previous_balance' => $currentBalance,
                    'new_balance' => $newBalance,
                    'transaction_amount' => $amount,
                    'transaction_type' => $transaction['type'],
                    'currency' => 'BDT'
                ];
            }

            return response()->json([
                'message' => 'Bulk balance update completed successfully',
                'results' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update balances',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}