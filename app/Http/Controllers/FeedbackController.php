<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Assessment;
use App\FeedbackLibrary;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    public function index()
    {
        $libraries = FeedbackLibrary::where('client_id', null)->get();
        return view('dashboard.feedback.index', compact('libraries'));
    }

    public function create()
    {
        $assessments = Assessment::all();
        return view('dashboard.feedback.create', compact('assessments'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $errors = [];

        $validator = Validator::make($data, [
            'name' => 'required|unique:feedback_libraries'
        ]);

        if ($validator->fails())
            return \Response::json(['errors' => ['Name must be unique.']]);

        if (!array_key_exists('feedback', $data))
            return \Response::json(['errors' => ['No feedback specified.']]);

        $library = new FeedbackLibrary([
            'name' => $data['name'],
            'feedback' => $data['feedback'],
        ]);

        try {
            $library->save();
        } catch (\Exception $e) {
            return \Response::json(['errors' => [$e->getMessage()]]);
        }

        return \Response::json(['success' => 'Saved successfully!']);
    }

    public function edit($id)
    {
        $library = FeedbackLibrary::findOrFail($id);
        $assessments = Assessment::all();
        return view('dashboard.feedback.edit', compact('library', 'assessments'));
    }

    public function update(Request $request, $id)
    {
        $library = FeedbackLibrary::findOrFail($id);
        $data = $request->all();
        $errors = [];

        $validator = Validator::make($data, [
            'name' => 'required|unique:feedback_libraries,name,'.$library->id,
        ]);

        if ($validator->fails())
            return \Response::json(['errors' => ['Name must be unique.']]);

        try {
            $library->update([
                'name' => $data['name'],
                'feedback' => $data['feedback'],
            ]);
        } catch (\Exception $e) {
            return \Response::json(['errors' => [$e->getMessage()]]);
        }

        return \Response::json(['success' => 'Updated successfully!']);
    }

    public function destroy($id)
    {
        $library = FeedbackLibrary::findOrFail($id);
        $library->delete();

        return redirect()->back()
            ->with('success', 'Feedback library "'.$library->name.'" deleted successfully!');
    }
}
