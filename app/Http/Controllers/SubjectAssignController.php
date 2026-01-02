<?php

namespace App\Http\Controllers;

use App\Models\SubjectAssign;
use App\Models\SubjectAssignDetail;
use App\Models\SubjectAssignStudent;
use App\Models\ClassWiseStudentData;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SubjectAssignController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query = SubjectAssign::with([
                'session:id,session_name',
                'version:id,version_name',
                'shift:id,shift_name',
                'class:id,class_name',
                'section:id,section_name',
                'details.subject:id,subject_name',
                'students.classWiseStudent.student:id,student_name'
            ]);

            foreach (['session_id', 'version_id', 'shift_id', 'class_id', 'section_id'] as $filter) {
                if ($request->filled($filter)) {
                    $query->where($filter, $request->$filter);
                }
            }

            $query->orderBy($request->get('sort_field', 'created_at'), $request->get('sort_order', 'desc'));
            $assignments = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'message' => 'Subject assignments retrieved successfully',
                'data' => $assignments
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve subject assignments',
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
            'subjects' => 'required|array|min:1',
            'subjects.*' => 'required|exists:subjects,id',
        ]);

        $existingAssignment = SubjectAssign::where('session_id', $request->session_id)
            ->where('shift_id', $request->shift_id)
            ->where('version_id', $request->version_id)
            ->where('class_id', $request->class_id)
            ->where('section_id', $request->section_id)
            ->first();

        if ($existingAssignment) {
            return response()->json([
                'success' => false,
                'message' => 'Subject assignment already exists for this combination'
            ], 422);
        }


        try {
            DB::beginTransaction();

            $subjectAssign = new SubjectAssign;
            $subjectAssign->session_id = $request->session_id;
            $subjectAssign->shift_id = $request->shift_id;
            $subjectAssign->version_id = $request->version_id;
            $subjectAssign->class_id = $request->class_id;
            $subjectAssign->section_id = $request->section_id;
            $subjectAssign->save();

            foreach ($request->subjects as $subjectId) {
                $AssignDetails = new SubjectAssignDetail;
                $AssignDetails->assign_id = $subjectAssign->id;
                $AssignDetails->subject_id = $subjectId;
                $AssignDetails->save();
            }

            $students = ClassWiseStudentData::where([
                'session_id' => $request->session_id,
                'version_id' => $request->version_id,
                'shift_id' => $request->shift_id,
                'class_id' => $request->class_id,
                'section_id' => $request->section_id
            ])->get();

            
            if ($students->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No students found for this class/section'
                ], 404);
            }

            foreach ($students as $student) {
                foreach ($request->subjects as $subjectId) {
                    $studentAssignDetails = new SubjectAssignStudent;
                    $studentAssignDetails->assign_id = $subjectAssign->id;
                    $studentAssignDetails->class_wise_student_id = $student->id;
                    $studentAssignDetails->subject_id = $subjectId;
                    $studentAssignDetails->save();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Subject assignment created successfully',
                'data' => $subjectAssign,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false, 
                'message' => 'Failed to create assignment', 
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $assignment = SubjectAssign::with([
                'session', 'version', 'shift', 'class', 'section',
                'details.subject', 'students.classWiseStudent.student', 'students.subject'
            ])->findOrFail($id);

            return response()->json(['success' => true, 'data' => $assignment]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }
    }

    public function update(Request $request, SubjectAssign $subjectAssign): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|exists:session_management,id',
            'version_id' => 'required|exists:version_management,id',
            'shift_id' => 'required|exists:shift_management,id',
            'class_id' => 'required|exists:class_management,id',
            'section_id' => 'required|exists:section_management,id',
            'subjects' => 'required|array|min:1',
            'subjects.*' => 'required|exists:subjects,id',
        ]);

        try {
            DB::beginTransaction();

            $subjectAssign->session_id = $request->session_id;
            $subjectAssign->shift_id = $request->shift_id;
            $subjectAssign->version_id = $request->version_id;
            $subjectAssign->class_id = $request->class_id;
            $subjectAssign->section_id = $request->section_id;
            $subjectAssign->save();

            SubjectAssignDetail::where('assign_id', $subjectAssign->id)->delete();

            foreach ($request->subjects as $subjectId) {
                $AssignDetails = new SubjectAssignDetail;
                $AssignDetails->assign_id = $subjectAssign->id;
                $AssignDetails->subject_id = $subjectId;
                $AssignDetails->save();
            }

            $students = ClassWiseStudentData::where([
                'session_id' => $request->session_id,
                'version_id' => $request->version_id,
                'shift_id' => $request->shift_id,
                'class_id' => $request->class_id,
                'section_id' => $request->section_id
            ])->get();

            if ($students->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No students found for this class/section'
                ], 404);
            }

            SubjectAssignStudent::where('assign_id', $subjectAssign->id)->delete();

            foreach ($students as $student) {
                foreach ($request->subjects as $subjectId) {
                    $studentAssignDetails = new SubjectAssignStudent;
                    $studentAssignDetails->assign_id = $subjectAssign->id;
                    $studentAssignDetails->class_wise_student_id = $student->id;
                    $studentAssignDetails->subject_id = $subjectId;
                    $studentAssignDetails->save();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Subject assignment updated successfully',
                'data' => $subjectAssign,
                'students_assigned' => $studentAssignDetails,
                'subjects_assigned' => count($request->subjects)
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false, 
                'message' => 'Failed to update assignment', 
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(SubjectAssign $subjectAssign): JsonResponse
    {
        try {
            $subjectAssign->delete();

            return response()->json([
                'success' => true,
                'message' => 'Subject Assign deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete subject Assign'
            ], 500);
        }
    }

    public function getStudentsByClass(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|exists:session_management,id',
            'version_id' => 'required|exists:version_management,id',
            'shift_id' => 'required|exists:shift_management,id',
            'class_id' => 'required|exists:class_management,id',
            'section_id' => 'required|exists:section_management,id'
        ]);

        try {
            $query = ClassWiseStudentData::with('student:id,student_name,student_image')
                ->where($request->only(['session_id', 'version_id', 'shift_id', 'class_id', 'section_id']));


            return response()->json(['success' => true, 'data' => $query->get()]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed', 'error' => $e->getMessage()], 500);
        }
    }
}

    

    