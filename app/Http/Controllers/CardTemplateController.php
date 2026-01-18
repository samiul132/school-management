<?php

namespace App\Http\Controllers;

use App\Models\CardTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class CardTemplateController extends Controller
{
    private ImageManager $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
    }

    public function index()
    {
        $templates = CardTemplate::all();

        $templates->transform(function ($item) {
            $item->teacher_image_url = $item->for_teacher_image
                ? asset('assets/images/card_templates/' . $item->for_teacher_image)
                : null;

            $item->student_image_url = $item->for_student_image
                ? asset('assets/images/card_templates/' . $item->for_student_image)
                : null;

            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $templates
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'school_id' => 'required',
            'teacher_template_file' => 'nullable|string',
            'student_template_file' => 'nullable|string',
        ]);

        $path = public_path('assets/images/card_templates');
        $templatesPath = public_path('assets/img/templates');
        
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $teacherImage = null;
        $studentImage = null;

        if ($request->has('teacher_template_file')) {
            $sourceFile = $templatesPath . '/' . $request->teacher_template_file;
            
            if (File::exists($sourceFile)) {
                $teacherImage = 'teacher_' . time() . '_' . Str::random(8) . '.jpg';

                $this->imageManager
                    ->read($sourceFile)
                    ->scale(width: 800)
                    ->save($path . '/' . $teacherImage);
            }
        }

        if ($request->has('student_template_file')) {
            $sourceFile = $templatesPath . '/' . $request->student_template_file;
            
            if (File::exists($sourceFile)) {
                $studentImage = 'student_' . time() . '_' . Str::random(8) . '.jpg';

                $this->imageManager
                    ->read($sourceFile)
                    ->scale(width: 800)
                    ->save($path . '/' . $studentImage);
            }
        }

        $template = CardTemplate::create([
            'school_id' => $request->school_id,
            'for_teacher_image' => $teacherImage,
            'for_student_image' => $studentImage,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Card template created successfully',
            'data' => $template
        ], 201);
    }

    public function show($id)
    {
        $template = CardTemplate::find($id);

        if (!$template) {
            return response()->json([
                'success' => false,
                'message' => 'Card template not found'
            ], 404);
        }

        $template->teacher_image_url = $template->for_teacher_image
            ? asset('assets/images/card_templates/' . $template->for_teacher_image)
            : null;

        $template->student_image_url = $template->for_student_image
            ? asset('assets/images/card_templates/' . $template->for_student_image)
            : null;

        return response()->json([
            'success' => true,
            'data' => $template
        ]);
    }

    public function update(Request $request, $id)
    {
        $template = CardTemplate::find($id);

        if (!$template) {
            return response()->json([
                'success' => false,
                'message' => 'Card template not found'
            ], 404);
        }

        $path = public_path('assets/images/card_templates');
        $templatesPath = public_path('assets/img/templates');

        if ($request->has('remove_teacher') && $request->remove_teacher === 'true') {
            if ($template->for_teacher_image && File::exists($path.'/'.$template->for_teacher_image)) {
                File::delete($path.'/'.$template->for_teacher_image);
            }
            $template->for_teacher_image = null;
        }
        elseif ($request->has('teacher_template_file')) {
            $sourceFile = $templatesPath . '/' . $request->teacher_template_file;
            
            if (File::exists($sourceFile)) {
                if ($template->for_teacher_image && File::exists($path.'/'.$template->for_teacher_image)) {
                    File::delete($path.'/'.$template->for_teacher_image);
                }

                $teacherImage = 'teacher_' . time() . '_' . Str::random(8) . '.jpg';
                
                $this->imageManager
                    ->read($sourceFile)
                    ->scale(width: 800)
                    ->save($path.'/'.$teacherImage);

                $template->for_teacher_image = $teacherImage;
            }
        }

        if ($request->has('remove_student') && $request->remove_student === 'true') {
            if ($template->for_student_image && File::exists($path.'/'.$template->for_student_image)) {
                File::delete($path.'/'.$template->for_student_image);
            }
            $template->for_student_image = null;
        }
        elseif ($request->has('student_template_file')) {
            $sourceFile = $templatesPath . '/' . $request->student_template_file;
            
            if (File::exists($sourceFile)) {
                if ($template->for_student_image && File::exists($path.'/'.$template->for_student_image)) {
                    File::delete($path.'/'.$template->for_student_image);
                }

                $studentImage = 'student_' . time() . '_' . Str::random(8) . '.jpg';
                
                $this->imageManager
                    ->read($sourceFile)
                    ->scale(width: 800)
                    ->save($path.'/'.$studentImage);

                $template->for_student_image = $studentImage;
            }
        }

        $template->save();

        return response()->json([
            'success' => true,
            'message' => 'Card template updated successfully',
            'data' => $template
        ]);
    }

    public function destroy($id)
    {
        $template = CardTemplate::find($id);

        if (!$template) {
            return response()->json([
                'success' => false,
                'message' => 'Card template not found'
            ], 404);
        }

        $path = public_path('assets/images/card_templates');

        if ($template->for_teacher_image && File::exists($path.'/'.$template->for_teacher_image)) {
            File::delete($path.'/'.$template->for_teacher_image);
        }

        if ($template->for_student_image && File::exists($path.'/'.$template->for_student_image)) {
            File::delete($path.'/'.$template->for_student_image);
        }

        $template->delete();

        return response()->json([
            'success' => true,
            'message' => 'Card template deleted successfully'
        ]);
    }
    
    public function getAvailableTemplates()
    {
        $templatesPath = public_path('assets/img/templates');
        
        if (!File::exists($templatesPath)) {
            return response()->json([
                'success' => true,
                'data' => [
                    'teacher' => [],
                    'student' => []
                ]
            ]);
        }
        
        $allFiles = File::files($templatesPath);
        $allTemplates = [];
        
        foreach ($allFiles as $file) {
            $filename = $file->getFilename();
            
            $extension = strtolower($file->getExtension());
            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                continue;
            }
            
            $allTemplates[] = asset('assets/img/templates/' . $filename);
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'teacher' => $allTemplates,  
                'student' => $allTemplates  
            ]
        ]);
    }
}
