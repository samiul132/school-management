<?php

namespace App\Http\Controllers;

use App\Models\Waver;
use App\Models\SessionManagement;
use App\Models\MonthManagement;
use App\Models\ClassManagement;
use App\Models\VersionManagement;
use App\Models\FeeHead;
use App\Models\SectionManagement;
use App\Models\ClassWiseStudentData;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class WaverController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Waver::with([
            'studentData.student', 
            'session',
            'version',
            'class',
            'section',
            'month',
            'feeHead'
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

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->filled('month_id')) {
            $query->where('month_id', $request->month_id);
        }

        if ($request->filled('head_id')) {
            $query->where('head_id', $request->head_id);
        }

        if ($request->filled('roll')) {
            $query->where('roll', 'LIKE', '%' . $request->roll . '%');
        }

        if ($request->filled('student_name')) {
            $query->whereHas('studentData.student', function($q) use ($request) {
                $q->where('student_name', 'LIKE', '%' . $request->student_name . '%');
            });
        }

        $sortField = $request->get('sort_field', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortField, $sortOrder);

        $perPage = $request->get('per_page', 10);
        $wavers = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Wavers retrieved successfully',
            'data' => $wavers
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'class_wise_student_id' => 'required|exists:class_wise_student_data,id',
            'session_id' => 'required|exists:session_management,id',
            'version_id' => 'required|exists:version_management,id',
            'class_id' => 'required|exists:class_management,id',
            'section_id' => 'required|exists:section_management,id',
            'month_id' => 'required|exists:month_management,id',
            'head_id' => 'required|exists:fee_heads,id',
            'roll' => 'nullable|integer|min:1',
            'waver_type' => 'required|in:fixed,percentage',
            'waver_amount' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $feeHead = FeeHead::find($request->head_id);
            if (!$feeHead || $feeHead->status != 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected fee head is not active'
                ], 422);
            }

            $existingWaver = Waver::where([
                'class_wise_student_id' => $request->class_wise_student_id,
                'month_id' => $request->month_id,
                'head_id' => $request->head_id,
            ])->first();

            if ($existingWaver) {
                DB::commit();
                return response()->json([
                    'success' => false,
                    'message' => 'Waver already exists for this student, month and fee head',
                    'data' => $existingWaver->load([
                        'studentData.student',
                        'session',
                        'version',
                        'class',
                        'section',
                        'month',
                        'feeHead'
                    ])
                ], 409);
            }

            $waver = new Waver;
            $waver->class_wise_student_id = $request->class_wise_student_id;
            $waver->session_id = $request->session_id;
            $waver->version_id = $request->version_id;
            $waver->class_id = $request->class_id;
            $waver->section_id = $request->section_id;
            $waver->month_id = $request->month_id;
            $waver->head_id = $request->head_id;
            $waver->roll = $request->roll;
            $waver->waver_type = $request->waver_type;
            $waver->waver_amount = $request->waver_amount;
            $waver->school_id = Auth::user()->school_id;
            $waver->save();

            $waver->load([
                'studentData.student',
                'session',
                'version',
                'class',
                'section',
                'month',
                'feeHead'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Waver created successfully',
                'data' => $waver
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create waver',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $waver = Waver::with([
                'studentData.student',
                'session',
                'version',
                'class',
                'section',
                'month',
                'feeHead'
            ])->find($id);

            if (!$waver) {
                return response()->json([
                    'success' => false,
                    'message' => 'Waver not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Waver retrieved successfully',
                'data' => $waver
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve waver',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'class_wise_student_id' => 'required|exists:class_wise_student_data,id',
            'session_id' => 'required|exists:session_management,id',
            'version_id' => 'required|exists:version_management,id',
            'class_id' => 'required|exists:class_management,id',
            'section_id' => 'required|exists:section_management,id',
            'month_id' => 'required|exists:month_management,id',
            'head_id' => 'required|exists:fee_heads,id',
            'roll' => 'nullable|integer|min:1',
            'waver_type' => 'required|in:fixed,percentage',
            'waver_amount' => 'required|numeric|min:0',
        ]);

        try {
            $waver = Waver::find($id);

            if (!$waver) {
                return response()->json([
                    'success' => false,
                    'message' => 'Waver not found'
                ], 404);
            }

            DB::beginTransaction();

            $feeHead = FeeHead::find($request->head_id);
            if (!$feeHead || $feeHead->status != 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected fee head is not active'
                ], 422);
            }

            $existingWaver = Waver::where([
                'class_wise_student_id' => $request->class_wise_student_id,
                'month_id' => $request->month_id,
                'head_id' => $request->head_id,
            ])->where('id', '!=', $id)->first();

            if ($existingWaver) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'A waver already exists for this student, month, and fee head'
                ], 409);
            }

            $waver->class_wise_student_id = $request->class_wise_student_id;
            $waver->session_id = $request->session_id;
            $waver->version_id = $request->version_id;
            $waver->class_id = $request->class_id;
            $waver->section_id = $request->section_id;
            $waver->month_id = $request->month_id;
            $waver->head_id = $request->head_id;
            $waver->roll = $request->roll;
            $waver->waver_type = $request->waver_type;
            $waver->waver_amount = $request->waver_amount;
            $waver->save();

            $waver->load([
                'studentData.student',
                'session',
                'version',
                'class',
                'section',
                'month',
                'feeHead'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Waver updated successfully',
                'data' => $waver
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update waver',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $waver = Waver::find($id);

            if (!$waver) {
                return response()->json([
                    'success' => false,
                    'message' => 'Waver not found'
                ], 404);
            }

            DB::beginTransaction();
            $waver->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Waver deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete waver',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:wavers,id'
        ]);

        try {
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();
            Waver::whereIn('id', $request->ids)->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Selected wavers deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete wavers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getDropdownData(): JsonResponse
    {
        try {
            $schoolId = Auth::user()->school_id;
            
            $sessions = SessionManagement::where('school_id', $schoolId)
                ->orderBy('session_name', 'desc')
                ->get(['id', 'session_name']);

            $activeSession = SessionManagement::where('school_id', $schoolId)
                ->where('status', 'active')
                ->orderBy('order_number', 'desc')
                ->first();
                    
            $months = MonthManagement::where('school_id', $schoolId)
                ->orderBy('id', 'asc') 
                ->get(['id', 'month_name']);
                
            $classes = ClassManagement::where('school_id', $schoolId)
                ->orderBy('id', 'asc')
                ->get(['id', 'class_name']);
                
            $versions = VersionManagement::where('school_id', $schoolId)
                ->orderBy('id', 'asc')
                ->get(['id', 'version_name']);
                
            $feeHeads = FeeHead::where('school_id', $schoolId)
                ->where('status', 'active')
                ->orderBy('head_name', 'asc')
                ->get(['id', 'head_name']);
                    
            $sections = SectionManagement::where('school_id', $schoolId)
                ->orderBy('section_name', 'asc')
                ->get(['id', 'section_name']);

            return response()->json([
                'success' => true,
                'data' => [
                    'sessions' => $sessions,
                    'active_session' => $activeSession,
                    'months' => $months,
                    'classes' => $classes,
                    'versions' => $versions,
                    'fee_heads' => $feeHeads,
                    'sections' => $sections,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get dropdown data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getStudentsByClassSection(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'class_id' => 'nullable|exists:class_management,id',
            'section_id' => 'nullable|exists:section_management,id',
            'session_id' => 'nullable|exists:session_management,id',
            'version_id' => 'nullable|exists:version_management,id',
            'roll' => 'nullable|integer|min:1',
            'id_card_number' => 'nullable|integer|min:1',
        ]);

        try {
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $schoolId = Auth::user()->school_id;

            $query = ClassWiseStudentData::with([
                'student',
                'class',
                'section',
                'session',
                'version'
            ])->where('school_id', $schoolId);

            if ($request->has('id_card_number') && $request->id_card_number) {
                $query->whereHas('student', function($q) use ($request) {
                    $q->where('id_card_number', $request->id_card_number);
                });
                
                if ($request->has('session_id') && $request->session_id) {
                    $query->where('session_id', $request->session_id);
                }
                if ($request->has('version_id') && $request->version_id) {
                    $query->where('version_id', $request->version_id);
                }
                if ($request->has('class_id') && $request->class_id) {
                    $query->where('class_id', $request->class_id);
                }
                if ($request->has('section_id') && $request->section_id) {
                    $query->where('section_id', $request->section_id);
                }
            } 
            else if ($request->has('roll') && $request->roll) {
                $query->where('class_roll', $request->roll);
                
                if ($request->has('session_id') && $request->session_id) {
                    $query->where('session_id', $request->session_id);
                }
                if ($request->has('version_id') && $request->version_id) {
                    $query->where('version_id', $request->version_id);
                }
                if ($request->has('class_id') && $request->class_id) {
                    $query->where('class_id', $request->class_id);
                }
                if ($request->has('section_id') && $request->section_id) {
                    $query->where('section_id', $request->section_id);
                }
            }
            else {
                if (!$request->has('class_id') || !$request->has('section_id') || 
                    !$request->has('session_id') || !$request->has('version_id')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'When roll and ID card number are not provided, all fields (session, version, class, section) are required'
                    ], 422);
                }
                
                $query->where([
                    'class_id' => $request->class_id,
                    'section_id' => $request->section_id,
                    'session_id' => $request->session_id,
                    'version_id' => $request->version_id,
                ]);
            }

            $students = $query->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'student_id' => $item->student_id,
                        'student' => $item->student ? [
                            'id' => $item->student->id,
                            'name' => $item->student->student_name,
                            'admission_no' => $item->student->admission_no,
                            'student_image_url' => $item->student->student_image_url,
                            'mobile_number' => $item->student->mobile_number,
                            'father_name' => $item->student->father_name,
                            'mother_name' => $item->student->mother_name,
                            'date_of_birth' => $item->student->date_of_birth,
                            'gender' => $item->student->gender,
                            'blood_group' => $item->student->blood_group,
                            'id_card_number' => $item->student->id_card_number,
                        ] : null,
                        'class_roll' => $item->class_roll,
                        'class_id' => $item->class_id,
                        'section_id' => $item->section_id,
                        'session_id' => $item->session_id,
                        'version_id' => $item->version_id,
                        'class' => $item->class,
                        'section' => $item->section,
                        'session' => $item->session,
                        'version' => $item->version,
                    ];
                })
                ->filter(function($item) {
                    return $item['student'] !== null;
                })
                ->values();

            return response()->json([
                'success' => true,
                'data' => $students
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get students',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getStudentWavers($class_wise_student_id): JsonResponse
    {
        try {
            $schoolId = Auth::user()->school_id;
            
            $wavers = Waver::where('school_id', $schoolId)
                ->where('class_wise_student_id', $class_wise_student_id)
                ->with(['feeHead', 'month', 'studentData.student'])
                ->get();
            
            if ($wavers->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No waivers found for this student'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Student waivers retrieved successfully',
                'data' => $wavers
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve student waivers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getStudentWaversForEdit($class_wise_student_id): JsonResponse
    {
        try {
            $schoolId = Auth::user()->school_id;
            
            $wavers = Waver::where('school_id', $schoolId)
                ->where('class_wise_student_id', $class_wise_student_id)
                ->with([
                    'studentData.student',
                    'session',
                    'version',
                    'class',
                    'section',
                    'month',
                    'feeHead'
                ])
                ->get();
            
            if ($wavers->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No waivers found for this student'
                ], 404);
            }
            
            $firstWaver = $wavers->first();
            $studentInfo = [
                'id' => $firstWaver->class_wise_student_id,
                'name' => $firstWaver->studentData->student->student_name ?? 'N/A',
                'roll' => $firstWaver->roll ?? $firstWaver->studentData->class_roll,
                'student_image_url' => $firstWaver->studentData->student->student_image ?? null,
                'id_card_number' => $firstWaver->studentData->student->id_card_number ?? null,
                'mobile_number' => $firstWaver->studentData->student->mobile_number ?? null,
                'father_name' => $firstWaver->studentData->student->father_name ?? null,
                'mother_name' => $firstWaver->studentData->student->mother_name ?? null,
                'session_id' => $firstWaver->session_id,
                'version_id' => $firstWaver->version_id,
                'class_id' => $firstWaver->class_id,
                'section_id' => $firstWaver->section_id,
            ];
            
            $organizedWavers = [];
            foreach ($wavers as $waver) {
                $monthId = $waver->month_id;
                $headId = $waver->head_id;
                
                if (!isset($organizedWavers[$monthId])) {
                    $organizedWavers[$monthId] = [
                        'month' => $waver->month,
                        'fee_heads' => []
                    ];
                }
                
                $organizedWavers[$monthId]['fee_heads'][$headId] = [
                    'id' => $waver->id,
                    'fee_head' => $waver->feeHead,
                    'waver_type' => $waver->waver_type,
                    'waver_amount' => $waver->waver_amount,
                ];
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Student waivers retrieved successfully',
                'data' => [
                    'student' => $studentInfo,
                    'wavers' => $organizedWavers,
                    'total_wavers' => $wavers->count(),
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve student waivers',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}