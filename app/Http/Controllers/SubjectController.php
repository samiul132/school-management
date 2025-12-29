<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SubjectController extends Controller
{

    public function index(): JsonResponse
    {
        try {
            $subjects = Subject::orderBy('order_number', 'asc')->get();
            return response()->json($subjects);
        } catch (\Exception $e) {
            Log::error('Error fetching subjects: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch subjects',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'subject_name' => 'required|string|max:255'
        ]);

        try {
            $subject = new Subject();
            $subject->subject_name = $request->subject_name;
            $subject->order_number = $request->order_number;
            $subject->subject_code = $request->subject_code;
            $subject->status = $request->status ?? 'active';
            $subject->save();

            return response()->json([
                'success' => true,
                'data' => $subject,
                'message' => 'Subject created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create subject'
            ], 500);
        }
    }

    public function show(Subject $subject): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $subject
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch subject'
            ], 500);
        }
    }

    public function update(Request $request, Subject $subject): JsonResponse
    {
        $request->validate([
            'subject_name' => 'required|string|max:255'
        ]);

        try {
            $subject->subject_name = $request->subject_name;
            $subject->order_number = $request->order_number;
            $subject->subject_code = $request->subject_code;
            if ($request->has('status')) {
                $subject->status = $request->status;
            }
            $subject->save();

            return response()->json([
                'success' => true,
                'data' => $subject,
                'message' => 'Subject updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update subsubjectsidiary'
            ], 500);
        }
    }

    public function destroy(Subject $subject): JsonResponse
    {
        try {
            $subject->delete();

            return response()->json([
                'success' => true,
                'message' => 'Subject deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete subject'
            ], 500);
        }
    }
}
