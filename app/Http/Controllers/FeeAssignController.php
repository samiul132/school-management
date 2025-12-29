<?php

namespace App\Http\Controllers;

use App\Models\FeeAssign;
use App\Models\FeeAssignDetails;
use App\Models\StudentWiseFeeAssign;
use App\Models\FeeHead;
use App\Models\SessionManagement;
use App\Models\VersionManagement;
use App\Models\ClassManagement;
use App\Models\MonthManagement;
use App\Models\ClassWiseStudentData;
use App\Models\CommonModel;
use App\Models\Waver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Auth;

class FeeAssignController extends Controller
{
    public function index(Request $request)
    {
        $query = FeeAssign::with([
            'session:id,session_name',
            'version:id,version_name',
            'class:id,class_name',  
            'month:id,month_name',
            'details.head:id,head_name'
        ]);

        if ($request->filled('session_id')) {
            $query->where('session_id', $request->session_id);
        }

        if ($request->filled('version_id')) {
            $query->where('version_id', $request->version_id);
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('month_id')) {
            $query->where('month_id', $request->month_id);
        }

        $feeAssigns = $query->latest()->paginate($request->per_page ?? 10);

        $feeAssigns->getCollection()->transform(function ($feeAssign) {
            $feeAssign->student_count = StudentWiseFeeAssign::where('assign_id', $feeAssign->id)
                ->distinct('class_wise_student_id')
                ->count();
            return $feeAssign;
        });

        return response()->json([
            'success' => true,
            'data' => $feeAssigns,
            'message' => 'Fee assigns retrieved successfully'
        ]);
    }

    public function store(Request $request)
    { 
        $validated = $request->validate([
            'session_id' => 'required|exists:session_management,id',
            'version_id' => 'required|exists:version_management,id',
            'class_id' => 'required|exists:class_management,id',
            'month_ids' => [
                'required',
                'array',
                'min:1',
                function ($attribute, $value, $fail) use ($request) {

                    if (count($value) !== count(array_unique($value))) {
                        $fail('The month_ids must not contain duplicate values.');
                        return;
                    }

                    $existingMonths = [];

                    foreach ($value as $monthId) {
                        $exists = FeeAssign::where([
                            'session_id' => $request->session_id,
                            'version_id' => $request->version_id,
                            'class_id'   => $request->class_id,
                            'month_id'   => $monthId,
                        ])->exists();

                        if ($exists) {
                            $monthName = MonthManagement::find($monthId)->month_name ?? 'Unknown';
                            $existingMonths[] = $monthName;
                        }
                    }

                    if (!empty($existingMonths)) {
                        $monthList = implode(', ', $existingMonths);
                        $fail("Fee assignment already exists for {$monthList}.");
                    }
                }
            ],
            'fee_heads' => 'required|array|min:1',
            'fee_heads.*.head_id' => 'required|exists:fee_heads,id',
            'fee_heads.*.amount' => 'required|numeric|min:0',
            'fee_heads.*.amount_type' => 'nullable|in:fixed,percentage',
            'apply_to_students' => 'boolean',
            'update_existing' => 'boolean' 
        ]);
        //dd('ok');
        DB::beginTransaction();
        try {
            

            $students = ClassWiseStudentData::select('id')
                ->where('session_id', $request->session_id)
                ->where('class_id', $request->class_id)
                ->where('version_id', $request->version_id)
                ->get();

            $studentsWaivers = Waver::select('class_wise_student_id', 'month_id', 'head_id', 'waver_type', 'waver_amount')
                ->where('session_id', $request->session_id)
                ->where('class_id', $request->class_id)
                ->where('version_id', $request->version_id)
                ->get();
            //dd($studentsWaivers);

            $feeAssignDetailsData = [];
            $stdWiseFeeData = [];
            foreach($request->month_ids as $monthId){
                $feeAssign = new FeeAssign;
                $feeAssign->session_id = $request->session_id;
                $feeAssign->version_id = $request->version_id;
                $feeAssign->class_id = $request->class_id;
                $feeAssign->month_id = $monthId;
                $feeAssign->total_amount = collect($request->fee_heads)->sum('amount');
                $feeAssign->save();

                foreach($request->fee_heads as $head){
                    if($head['amount']>0){
                        $feeAssignDetailsData[] = [
                            'fee_assign_id' => $feeAssign->id,
                            'head_id' => $head['head_id'],
                            'amount' => $head['amount'],
                        ];

                        foreach($students as $std){
                            $waiver = collect($studentsWaivers)->first(function ($item) use ($std, $monthId, $head) {
                                return $item['class_wise_student_id'] == $std->id
                                    && $item['month_id'] == $monthId
                                    && $item['head_id'] == $head['head_id'];
                            });
                            if($waiver!=NULL){
                                if($waiver->waver_type=='percentage'){
                                    $waiverAmount = ($head['amount'] * $waiver->waver_amount) / 100;
                                } else {
                                    $waiverAmount = $waiver->waver_amount;
                                }
                                $paybleAmount = $head['amount'] - $waiverAmount;
                            } else {
                                $waiverAmount = 0;
                                $paybleAmount = $head['amount'];
                            }


                            $stdWiseFeeData[] = [
                                'assign_id' => $feeAssign->id,
                                'head_id' => $head['head_id'],
                                'amount' => $head['amount'],
                                'waver_amount' => $waiverAmount,
                                'month_id' => $monthId,
                                'class_wise_student_id' => $std->id,
                                'payble_amount' => $paybleAmount,
                                'paid_amount' => 0,
                                'due_amount' => $paybleAmount,
                            ];
                        }
                    }
                }
            }

            FeeAssignDetails::bulkInsert($feeAssignDetailsData);
            StudentWiseFeeAssign::bulkInsert($stdWiseFeeData);

            foreach($students as $std){
                CommonModel::updateFeePayment($request->class_wise_student_id);
            }

            DB::commit();

            $message = [];
            $message[] = "Created fee assignment";
            

            return response()->json([
                'success' => true,
                'message' => implode(' and ', $message),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Fee assignment error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save fee assignment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $feeAssign = FeeAssign::with([
                'session:id,session_name',
                'version:id,version_name',
                'class:id,class_name',
                'month:id,month_name',
            ])->findOrFail($id);

            $details = FeeAssignDetails::withoutGlobalScope('school')
                ->with('head:id,head_name')
                ->where('fee_assign_id', $id)
                ->get();
            
            $feeAssign->details = $details;

            $students = StudentWiseFeeAssign::with([
                'student.student:id,student_name',
                'feeHead:id,head_name'  
            ])->where('assign_id', $id)->get()->groupBy('class_wise_student_id');

            return response()->json([
                'success' => true,
                'data' => [
                    'fee_assign' => $feeAssign,
                    'students' => $students
                ],
                'message' => 'Fee assign details retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve fee assign details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $feeAssign = FeeAssign::findOrFail($id);
            
            FeeAssignDetails::where('fee_assign_id', $feeAssign->id)->delete();
            StudentWiseFeeAssign::where('assign_id', $feeAssign->id)->delete();

            $students = ClassWiseStudentData::select('id')
                ->where('session_id', $request->session_id)
                ->where('class_id', $request->class_id)
                ->where('version_id', $request->version_id)
                ->get();

            $studentsWaivers = Waver::select('class_wise_student_id', 'month_id', 'head_id', 'waver_type', 'waver_amount')
                ->where('session_id', $request->session_id)
                ->where('class_id', $request->class_id)
                ->where('version_id', $request->version_id)
                ->get();

            $feeAssignDetailsData = [];
            $stdWiseFeeData = [];
            
            foreach($request->month_ids as $monthId){
                $feeAssign->total_amount = collect($request->fee_heads)->sum('amount');
                $feeAssign->save();

                foreach($request->fee_heads as $head){
                    if($head['amount']>0){
                        $feeAssignDetailsData[] = [
                            'fee_assign_id' => $feeAssign->id,
                            'head_id' => $head['head_id'],
                            'amount' => $head['amount'],
                        ];

                        foreach($students as $std){
                            $waiver = collect($studentsWaivers)->first(function ($item) use ($std, $monthId, $head) {
                                return $item['class_wise_student_id'] == $std->id
                                    && $item['month_id'] == $monthId
                                    && $item['head_id'] == $head['head_id'];
                            });
                            if($waiver!=NULL){
                                if($waiver->waver_type=='percentage'){
                                    $waiverAmount = ($head['amount'] * $waiver->waver_amount) / 100;
                                } else {
                                    $waiverAmount = $waiver->waver_amount;
                                }
                                $paybleAmount = $head['amount'] - $waiverAmount;
                            } else {
                                $waiverAmount = 0;
                                $paybleAmount = $head['amount'];
                            }


                            $stdWiseFeeData[] = [
                                'assign_id' => $feeAssign->id,
                                'head_id' => $head['head_id'],
                                'amount' => $head['amount'],
                                'waver_amount' => $waiverAmount,
                                'month_id' => $monthId,
                                'class_wise_student_id' => $std->id,
                                'payble_amount' => $paybleAmount,
                                'paid_amount' => 0,
                                'due_amount' => $paybleAmount,
                            ];
                        }
                    }
                }
            }

            FeeAssignDetails::bulkInsert($feeAssignDetailsData);
            StudentWiseFeeAssign::bulkInsert($stdWiseFeeData);

            foreach($students as $std){
                CommonModel::updateFeePayment($std->id);
            }

            DB::commit();

            $message = [];
            $message[] = "Updated fee assignment";
            

            return response()->json([
                'success' => true,
                'message' => implode(' and ', $message),
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Fee assignment error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update fee assignment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        
        try {
            $FeeAssign = FeeAssign::findOrFail($id);

            FeeAssignDetails::where('fee_assign_id', $id)->delete();
            StudentWiseFeeAssign::where('assign_id', $id)->delete();

            $FeeAssign->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Fee assign deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete fee assign',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getDropdownData()
    {
        try {
            $sessions = SessionManagement::where('status', 'active')
                ->get(['id', 'session_name'])
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->session_name,
                        'session_name' => $item->session_name
                    ];
                })->toArray();
                
            $versions = VersionManagement::where('status', 'active')
                ->get(['id', 'version_name'])
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->version_name,
                        'version_name' => $item->version_name
                    ];
                })->toArray();
                
            $classes = ClassManagement::where('status', 'active')
                ->get(['id', 'class_name'])
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->class_name,
                        'class_name' => $item->class_name
                    ];
                })->toArray();
                
            $months = MonthManagement::where('status', 'active')
                ->get(['id', 'month_name'])
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->month_name,
                        'month_name' => $item->month_name
                    ];
                })->toArray();
                
            $feeHeads = FeeHead::where('status', 'active')
                ->get(['id', 'head_name'])
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->head_name,
                        'head_name' => $item->head_name
                    ];
                })->toArray();

            return response()->json([
                'success' => true,
                'data' => [
                    'sessions' => $sessions,
                    'versions' => $versions,
                    'classes' => $classes,
                    'months' => $months,
                    'fee_heads' => $feeHeads
                ],
                'message' => 'Dropdown data retrieved successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to retrieve dropdown data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve dropdown data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getStudentsByFilter(Request $request)
    {
        try {
            if ($request->has('class_wise_student_id')) {
                $validated = $request->validate([
                    'class_wise_student_id' => 'required|exists:class_wise_student_data,id'
                ]);

                $students = ClassWiseStudentData::with([
                    'student',
                    'session',
                    'class',
                    'version',
                    'section',
                    'studentWiseFeeAssigns' => function ($query) {
                        $query->with('feeHead:id,head_name,head_type')
                            ->where('school_id', Auth::user()->school_id)
                            ->select('student_wise_fee_assigns.*')
                            ->orderBy('updated_at', 'desc');
                    }
                ])
                ->where('id', $validated['class_wise_student_id'])
                ->get();

                foreach ($students as $student) {
                    $groupedFees = $student->studentWiseFeeAssigns
                        ->groupBy('head_id')
                        ->map(function ($items) {
                            return $items->first(); 
                        })
                        ->values();

                    $student->student_wise_fee_assigns = $groupedFees->map(function ($fee) {
                        return [
                            'id' => $fee->id,
                            'assign_id' => $fee->assign_id,
                            'head_id' => $fee->head_id,
                            'class_wise_student_id' => $fee->class_wise_student_id,
                            'amount' => $fee->amount,
                            'waver_amount' => $fee->waver_amount,
                            'payble_amount' => $fee->payble_amount,
                            'created_at' => $fee->created_at,
                            'updated_at' => $fee->updated_at,
                            'fee_head' => [
                                'id' => $fee->feeHead->id,
                                'head_name' => $fee->feeHead->head_name,
                                'head_type' => $fee->feeHead->head_type
                            ]
                        ];
                    });

                    unset($student->studentWiseFeeAssigns);
                }

                return response()->json([
                    'success' => true,
                    'data' => $students
                ], 200);
            }

            $validated = $request->validate([
                'session_id' => 'required|exists:session_management,id',
                'class_id' => 'required|exists:class_management,id',
                'version_id' => 'required|exists:version_management,id',
                'section_id' => 'nullable|exists:section_management,id',
                'roll' => 'nullable|integer',
                'id_card_number' => 'nullable|string',
            ]);

            $query = ClassWiseStudentData::with([
                'student',
                'session',
                'class',
                'version',
                'section',
                'studentWiseFeeAssigns' => function ($query) {
                    $query->with('feeHead:id,head_name,head_type')
                        ->where('school_id', Auth::user()->school_id)
                        ->select('student_wise_fee_assigns.*')
                        ->orderBy('updated_at', 'desc');
                }
            ])
            ->where('session_id', $validated['session_id'])
            ->where('class_id', $validated['class_id'])
            ->where('version_id', $validated['version_id']);

            if (isset($validated['section_id'])) {
                $query->where('section_id', $validated['section_id']);
            }

            if (isset($validated['roll'])) {
                $query->where('class_roll', $validated['roll']);
            }

            if (isset($validated['id_card_number'])) {
                $query->whereHas('student', function($q) use ($validated) {
                    $q->where('id_card_number', $validated['id_card_number']);
                });
            }

            $students = $query->get();

            foreach ($students as $student) {
                $groupedFees = $student->studentWiseFeeAssigns
                    ->groupBy('head_id')
                    ->map(function ($items) {
                        return $items->first(); 
                    })
                    ->values();

                $student->student_wise_fee_assigns = $groupedFees->map(function ($fee) {
                    return [
                        'id' => $fee->id,
                        'assign_id' => $fee->assign_id,
                        'head_id' => $fee->head_id,
                        'class_wise_student_id' => $fee->class_wise_student_id,
                        'amount' => $fee->amount,
                        'waver_amount' => $fee->waver_amount,
                        'payble_amount' => $fee->payble_amount,
                        'created_at' => $fee->created_at,
                        'updated_at' => $fee->updated_at,
                        'fee_head' => [
                            'id' => $fee->feeHead->id,
                            'head_name' => $fee->feeHead->head_name,
                            'head_type' => $fee->feeHead->head_type
                        ]
                    ];
                });

                unset($student->studentWiseFeeAssigns);
            }

            return response()->json([
                'success' => true,
                'data' => $students
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch students',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}