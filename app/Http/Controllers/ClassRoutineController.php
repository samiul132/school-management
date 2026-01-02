<?php

namespace App\Http\Controllers;

use App\Models\ClassRoutine;
use App\Models\ClassRoutineDetails;
use App\Models\SessionManagement;
use App\Models\VersionManagement;
use App\Models\ShiftManagement;
use App\Models\ClassManagement;
use App\Models\SectionManagement;
use App\Models\Subject;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ClassRoutineController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query = ClassRoutine::with([
                'session:id,session_name',
                'version:id,version_name',
                'shift:id,shift_name',
                'class:id,class_name',
                'section:id,section_name',
                'details.subject:id,subject_name',
                'details.teacher:id,first_name,last_name'
            ]);

            foreach (['session_id', 'version_id', 'shift_id', 'class_id', 'section_id'] as $filter) {
                if ($request->filled($filter)) {
                    $query->where($filter, $request->$filter);
                }
            }

            $query->orderBy($request->get('sort_field', 'created_at'), $request->get('sort_order', 'desc'));
            
            $classRoutines = $query->paginate($request->get('per_page', 10));

            return response()->json([
                'success' => true,
                'message' => 'Class routines retrieved successfully',
                'data' => $classRoutines
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve class routines',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getDropdownData(): JsonResponse
    {
        try {
            $activeSession = SessionManagement::where('status', 'active')
                ->select('id', 'session_name', 'status')
                ->first();
            $data = [
                'sessions' => SessionManagement::select('id', 'session_name', 'status')
                    ->orderBy('session_name')
                    ->get(),    
                'versions' => VersionManagement::select('id', 'version_name')
                    ->orderBy('version_name')
                    ->get(),
                'shifts' => ShiftManagement::select('id', 'shift_name')
                    ->orderBy('shift_name')
                    ->get(),
                'classes' => ClassManagement::select('id', 'class_name')
                    ->orderBy('class_name')
                    ->get(),
                'sections' => SectionManagement::select('id', 'section_name')
                    ->orderBy('section_name')
                    ->get(),
                'subjects' => Subject::select('id', 'subject_name', 'subject_code')
                    ->orderBy('subject_name')
                    ->get(),
                'teachers' => Staff::select('id', 'first_name', 'last_name', 'is_teachers')
                    ->where('is_teachers', 1)
                    ->orderBy('first_name')
                    ->get(),
                'active_session' => $activeSession,
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load dropdown data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|exists:session_management,id',
            'version_id' => 'required|exists:version_management,id',
            'shift_id' => 'required|exists:shift_management,id',
            'class_id' => 'required|exists:class_management,id',
            'section_id' => 'required|exists:section_management,id',
            'number_of_periods' => 'required|integer|min:1',
            'routine_details' => 'required|array|min:1',
            'routine_details.*.subject_id' => 'required|exists:subjects,id',
            'routine_details.*.teacher_id' => 'required|exists:staff,id',
            'routine_details.*.period_number' => 'required|integer',
            'routine_details.*.day_name' => 'required|string',
            'routine_details.*.time' => 'required|string',
        ]);

        $existingRoutine = ClassRoutine::where('session_id', $request->session_id)
            ->where('shift_id', $request->shift_id)
            ->where('version_id', $request->version_id)
            ->where('class_id', $request->class_id)
            ->where('section_id', $request->section_id)
            ->first();

        if ($existingRoutine) {
            return response()->json([
                'success' => false,
                'message' => 'Class routine already exists for this combination'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $classRoutine = new ClassRoutine;
            $classRoutine->session_id = $request->session_id;
            $classRoutine->version_id = $request->version_id;
            $classRoutine->shift_id = $request->shift_id;
            $classRoutine->class_id = $request->class_id;
            $classRoutine->section_id = $request->section_id;
            $classRoutine->number_of_periods = $request->number_of_periods;
            $classRoutine->off_day = $request['off_day'];
            $classRoutine->save();

            foreach ($request->routine_details as $detail) {
                $classRoutineDetails = new ClassRoutineDetails;
                $classRoutineDetails->class_routine_id = $classRoutine->id;
                $classRoutineDetails->subject_id = $detail['subject_id'];
                $classRoutineDetails->teacher_id = $detail['teacher_id'];
                $classRoutineDetails->period_number = $detail['period_number'];
                $classRoutineDetails->day_name = $detail['day_name'];
                $classRoutineDetails->time = $detail['time'];
                $classRoutineDetails->save();
            }

            DB::commit();

            $classRoutine->load([
                'session', 'version', 'shift', 'class', 'section',
                'details.subject', 'details.teacher'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Class routine created successfully',
                'data' => $classRoutine,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false, 
                'message' => 'Failed to create routine', 
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $routine = ClassRoutine::with([
                'session', 'version', 'shift', 'class', 'section',
                'details.subject', 'details.teacher',
            ])->findOrFail($id);

            $offDays = [];
            if ($routine->off_day) {
                $offDays = explode(',', $routine->off_day);
            }

            $filteredDetails = $routine->details->filter(function($detail) use ($offDays) {
                return !in_array($detail->day_name, $offDays);
            })->values(); 

            $routine->setRelation('details', $filteredDetails);

            return response()->json([
                'success' => true, 
                'data' => $routine
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Class routine not found'
            ], 404);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|exists:session_management,id',
            'version_id' => 'required|exists:version_management,id',
            'shift_id' => 'required|exists:shift_management,id',
            'class_id' => 'required|exists:class_management,id',
            'section_id' => 'required|exists:section_management,id',
            'number_of_periods' => 'required|integer|min:1',
            'routine_details' => 'required|array|min:1',
            'routine_details.*.subject_id' => 'required|exists:subjects,id',
            'routine_details.*.teacher_id' => 'required|exists:staff,id',
            'routine_details.*.period_number' => 'required|integer',
            'routine_details.*.day_name' => 'required|string',
            'routine_details.*.time' => 'required|string',
        ]);

        try {
            $classRoutine = ClassRoutine::findOrFail($id);

            $existingRoutine = ClassRoutine::where('id', '!=', $id)
                ->where('session_id', $request->session_id)
                ->where('shift_id', $request->shift_id)
                ->where('version_id', $request->version_id)
                ->where('class_id', $request->class_id)
                ->where('section_id', $request->section_id)
                ->first();

            if ($existingRoutine) {
                return response()->json([
                    'success' => false,
                    'message' => 'Class routine already exists for this combination'
                ], 422);
            }

            DB::beginTransaction();

            $classRoutine->session_id = $request->session_id;
            $classRoutine->version_id = $request->version_id;
            $classRoutine->shift_id = $request->shift_id;
            $classRoutine->class_id = $request->class_id;
            $classRoutine->section_id = $request->section_id;
            $classRoutine->number_of_periods = $request->number_of_periods;
            $classRoutine->off_day = $request['off_day'];
            $classRoutine->save();

            ClassRoutineDetails::where('class_routine_id', $classRoutine->id)->delete();

            foreach ($request->routine_details as $detail) {
                $classRoutineDetails = new ClassRoutineDetails;
                $classRoutineDetails->class_routine_id = $classRoutine->id;
                $classRoutineDetails->subject_id = $detail['subject_id'];
                $classRoutineDetails->teacher_id = $detail['teacher_id'];
                $classRoutineDetails->period_number = $detail['period_number'];
                $classRoutineDetails->day_name = $detail['day_name'];
                $classRoutineDetails->time = $detail['time'];
                $classRoutineDetails->save();
            }

            DB::commit();

            $classRoutine->load([
                'session', 'version', 'shift', 'class', 'section',
                'details.subject', 'details.teacher'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Class routine updated successfully',
                'data' => $classRoutine
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update routine',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $classRoutine = ClassRoutine::findOrFail($id);
            $classRoutine->delete();

            return response()->json([
                'success' => true,
                'message' => 'Class routine deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete class routine',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}