<?php

namespace App\Http\Controllers;

use App\FeedbackLibrary;
use App\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    /**
     * Display a listing of feedback libraries.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $libraries = FeedbackLibrary::with('client')
            ->where('client_id', null)
            ->orWhere('client_id', Auth::user()->client_id)
            ->orderBy('name')
            ->get();

        // Get all assessments with their dimensions to populate the sidebar tabs
        $assessments = Assessment::with('dimensions')->orderBy('name')->get();

        return view('dashboard.feedback.index', compact('libraries', 'assessments'));
    }

    /**
     * Show the form for creating a new feedback library.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $clients = Client::orderBy('name')->get();
        return view('dashboard.feedback.create', compact('clients'));
    }

    /**
     * Store a newly created feedback library.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:feedback_libraries,name',
            'client_id' => 'nullable|exists:clients,id',
            'feedback' => 'required|json'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate JSON structure
        $feedbackData = json_decode($request->feedback, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            if ($request->ajax()) {
                return response()->json(['errors' => ['feedback' => ['Invalid JSON format']]], 422);
            }
            return redirect()->back()
                ->withErrors(['feedback' => 'Invalid JSON format'])
                ->withInput();
        }

        // Validate feedback structure
        $validationResult = $this->validateFeedbackStructure($feedbackData);
        if (!$validationResult['valid']) {
            if ($request->ajax()) {
                return response()->json(['errors' => ['feedback' => $validationResult['errors']]], 422);
            }
            return redirect()->back()
                ->withErrors(['feedback' => implode(', ', $validationResult['errors'])])
                ->withInput();
        }

        $library = new FeedbackLibrary([
            'name' => $request->name,
            'client_id' => $request->client_id,
            'feedback' => $feedbackData,
        ]);

        $library->save();

        if ($request->ajax()) {
            return response()->json(['success' => 'Feedback library created successfully!']);
        }

        return redirect()->route('feedback.index')
            ->with('success', 'Feedback library created successfully!');
    }

    /**
     * Display the specified feedback library.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $library = FeedbackLibrary::with('client')->findOrFail($id);
        return view('dashboard.feedback.show', compact('library'));
    }

    /**
     * Show the form for editing the specified feedback library.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $library = FeedbackLibrary::findOrFail($id);
        $clients = Client::orderBy('name')->get();
        return view('dashboard.feedback.edit', compact('library', 'clients'));
    }

    /**
     * Update the specified feedback library.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $library = FeedbackLibrary::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:feedback_libraries,name,' . $id,
            'client_id' => 'nullable|exists:clients,id',
            'feedback' => 'required|json'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate JSON structure
        $feedbackData = json_decode($request->feedback, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            if ($request->ajax()) {
                return response()->json(['errors' => ['feedback' => ['Invalid JSON format']]], 422);
            }
            return redirect()->back()
                ->withErrors(['feedback' => 'Invalid JSON format'])
                ->withInput();
        }

        // Validate feedback structure
        $validationResult = $this->validateFeedbackStructure($feedbackData);
        if (!$validationResult['valid']) {
            if ($request->ajax()) {
                return response()->json(['errors' => ['feedback' => $validationResult['errors']]], 422);
            }
            return redirect()->back()
                ->withErrors(['feedback' => implode(', ', $validationResult['errors'])])
                ->withInput();
        }

        $library->update([
            'name' => $request->name,
            'client_id' => $request->client_id,
            'feedback' => $feedbackData,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => 'Feedback library updated successfully!']);
        }

        return redirect()->route('feedback.index')
            ->with('success', 'Feedback library updated successfully!');
    }

    /**
     * Remove the specified feedback library.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $library = FeedbackLibrary::findOrFail($id);
        $library->delete();

        if (request()->ajax()) {
            return response()->json(['success' => 'Feedback library deleted successfully!']);
        }

        return redirect()->route('feedback.index')
            ->with('success', 'Feedback library deleted successfully!');
    }

    /**
     * Validate the structure of feedback data.
     *
     * @param array $feedbackData
     * @return array
     */
    private function validateFeedbackStructure($feedbackData)
    {
        $errors = [];

        if (!isset($feedbackData['dimensions']) || !is_array($feedbackData['dimensions'])) {
            $errors[] = 'Feedback must contain a "dimensions" object';
            return ['valid' => false, 'errors' => $errors];
        }

        if (empty($feedbackData['dimensions'])) {
            $errors[] = 'At least one dimension is required';
            return ['valid' => false, 'errors' => $errors];
        }

        foreach ($feedbackData['dimensions'] as $dimensionName => $dimensionData) {
            if (!is_array($dimensionData)) {
                $errors[] = "Dimension '{$dimensionName}' must be an object";
                continue;
            }

            $requiredLevels = ['high', 'medium', 'low'];
            foreach ($requiredLevels as $level) {
                if (!isset($dimensionData[$level]) || empty(trim($dimensionData[$level]))) {
                    $errors[] = "Dimension '{$dimensionName}' must have '{$level}' feedback";
                }
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Get feedback libraries for API.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiIndex()
    {
        $libraries = FeedbackLibrary::with('client')
            ->where('client_id', null)
            ->orWhere('client_id', Auth::user()->client_id)
            ->orderBy('name')
            ->get();

        return response()->json([
            'libraries' => $libraries
        ]);
    }

    /**
     * Generate feedback for a specific user and assessment.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateFeedback(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'assessment_id' => 'required|exists:assessments,id',
            'scores' => 'required|array'
        ]);

        $user = \App\User::findOrFail($request->user_id);
        $assessment = \App\Assessment::findOrFail($request->assessment_id);

        $feedbackService = app('App\Services\FeedbackService');
        $feedback = $feedbackService->generateFeedback($user, $assessment, $request->scores);

        return response()->json([
            'feedback' => $feedback,
            'generated_at' => now()->toISOString()
        ]);
    }

    /**
     * Get feedback library by type
     *
     * @param string $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByType($type)
    {
        $library = FeedbackLibrary::where('name', 'like', '%' . $type . '%')->first();
        
        if (!$library) {
            return response()->json([
                'success' => false,
                'message' => 'Library not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'library' => $library
        ]);
    }

    /**
     * Save feedback data for a specific library type
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveFeedback(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'library_type' => 'required|string',
            'name' => 'required|string|max:255',
            'dimensions' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Find or create the library
        // First try to find by exact name match
        $library = FeedbackLibrary::where('name', $request->name)->first();
        
        // If not found, try to find by library_type in feedback data (compatible approach)
        if (!$library) {
            $libraries = FeedbackLibrary::all();
            foreach ($libraries as $lib) {
                if (isset($lib->feedback['library_type']) && $lib->feedback['library_type'] === $request->library_type) {
                    $library = $lib;
                    break;
                }
            }
        }
        
        // If still not found, try pattern matching
        if (!$library) {
            $library = FeedbackLibrary::where('name', 'like', '%' . $request->library_type . '%')->first();
        }
        
        // If still not found, create new library
        if (!$library) {
            $library = new FeedbackLibrary();
        }

        $feedbackData = [
            'library_type' => $request->library_type,
            'dimensions' => $request->dimensions
        ];

        $library->name = $request->name;
        $library->feedback = $feedbackData;
        $library->save();

        return response()->json([
            'success' => true,
            'message' => 'Feedback saved successfully.',
            'library' => $library
        ]);
    }
}