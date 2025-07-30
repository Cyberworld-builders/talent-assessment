<?php

namespace App\Http\Controllers;

use App\Assessment;
use App\Language;
use App\TranslatedQuestion;
use App\Translation;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class TranslationsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $assessment = Assessment::findOrFail($id);
        $translations = $assessment->translations;

        return view('dashboard.translations.index', compact('assessment', 'translations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $assessment = Assessment::findOrFail($id);
        $questions = $assessment->questions;

        $exclude_languages = [];
        $existing_translations = Translation::where('assessment_id', $assessment->id)->get();
        if ($existing_translations)
        {
            foreach ($existing_translations as $translation)
                array_push($exclude_languages, $translation->language_id);
        }
        array_push($exclude_languages, 1);

        $languages = Language::all();
        $languages_array = ['' => 'Select your language...'];
        foreach ($languages as $language)
        {
            if (in_array($language->id, $exclude_languages))
                continue;

            $languages_array[$language->id] = $language->name;
        }

        return view('dashboard.translations.create', compact('assessment', 'questions', 'languages_array'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $id
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store($id, Request $request)
    {
        $assessment = Assessment::findOrFail($id);
        $assessment_data = $request->except('questions');
        $question_data = $request->get('questions');

        // Store translation
        $translation = new Translation($assessment_data);
        $translation->assessment_id = $assessment->id;
        \Auth::user()->translations()->save($translation);

        // Store the questions
        foreach ($question_data as $data)
        {
            $question = new TranslatedQuestion($data);
            $translation->questions()->save($question);
        }

        return \Response::json([
            'success' => true,
            'redirect' => '/dashboard/assessments/'.$assessment->id.'/translations/'.$translation->id.'/edit'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, $translationId)
    {
        $assessment = Assessment::findOrFail($id);
        $translation = Translation::findOrFail($translationId);
        $questions = $assessment->questions;
        $translated_questions = $translation->questions;

        $exclude_languages = [];
        $existing_translations = Translation::where('assessment_id', $assessment->id)->get();
        if ($existing_translations)
        {
            foreach ($existing_translations as $translation)
                array_push($exclude_languages, $translation->language_id);
        }
        array_push($exclude_languages, 1);

        $languages = Language::all();
        $languages_array = [$translation->language()->id => $translation->language()->name];
        foreach ($languages as $language)
        {
            if (in_array($language->id, $exclude_languages))
                continue;

            $languages_array[$language->id] = $language->name;
        }

        return view('dashboard.translations.edit', compact('translation', 'assessment', 'questions', 'languages_array', 'translated_questions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @param  \Illuminate\Http\Request $request
     * @param $translationId
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request, $translationId)
    {
        $assessment = Assessment::findOrFail($id);
        $translation = Translation::findOrFail($translationId);
        $assessment_data = $request->except('questions');
        $question_data = $request->get('questions');

        // Update translation
        $translation->update($assessment_data);

        // Update the questions
        foreach ($question_data as $data)
        {
            $question = TranslatedQuestion::find($data['question_id']);
            unset($data['question_id']);
            $question->update($data);
        }

        return \Response::json([
            'success' => true,
            'redirect' => '/dashboard/assessments/'.$assessment->id.'/translations/'.$translation->id.'/edit'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $translationId)
    {
        $assessment = Assessment::findOrFail($id);
        $translation = Translation::findOrFail($translationId);

        $translation->delete();

        return redirect('dashboard/assessments/'.$assessment->id.'/translations')->with('success', $translation->language()->name.' translation of '.$assessment->name.' deleted successfully!');
    }
}
