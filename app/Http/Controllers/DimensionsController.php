<?php

namespace App\Http\Controllers;

use App\Assessment;
use App\Dimension;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class DimensionsController extends Controller
{

    /**
     * Display a listing of the dimensions.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $assessment = Assessment::findOrFail($id);
        $dimensions = Dimension::where([
            'assessment_id' => $assessment->id,
            'parent' => 0
        ])->get();

        return view('dashboard.dimensions.index', compact('assessment', 'dimensions'));
    }

    /**
     * Show the form for creating a new dimension.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $assessment = Assessment::findOrFail($id);
        $dimensions = Dimension::where([
            'assessment_id' => $assessment->id,
            'parent' => 0
        ])->get();

        $dimensionsArray = [];
        foreach ($dimensions as $dim)
            $dimensionsArray = array_add($dimensionsArray, $dim->id, $dim->name);

        return view('dashboard.dimensions.create', compact('assessment', 'dimensionsArray'));
    }

    /**
     * Store a newly created dimension in storage.
     *
     * @param $id
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store($id, Request $request)
    {
        $assessment = Assessment::findOrFail($id);
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'required',
            'code' => 'required|min:1'
        ]);

        if ($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());

        if (! $data['is_sub'])
            $data['parent'] = 0;

        $dimension = new Dimension($data);
        $assessment->dimensions()->save($dimension);

        return redirect('dashboard/assessments/'.$assessment->id.'/dimensions')->with('success', 'Dimension '.$dimension->name.' created successfully!');
    }

    /**
     * Display the specified dimension.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified dimension.
     *
     * @param  int $id
     * @param $dimensionId
     * @return \Illuminate\Http\Response
     */
    public function edit($id, $dimensionId)
    {
        $assessment = Assessment::findOrFail($id);
        $dimension = Dimension::findOrFail($dimensionId);
        if ($dimension->parent != 0)
            $dimension->is_sub = 1;

        $dimensions = Dimension::where([
            'assessment_id' => $assessment->id,
            'parent' => 0
        ])->get();

        $dimensionsArray = [];
        foreach ($dimensions as $dim)
            if ($dim->id != $dimension->id)
                $dimensionsArray = array_add($dimensionsArray, $dim->id, $dim->name);

        return view('dashboard.dimensions.edit', compact('assessment', 'dimension', 'dimensionsArray'));
    }

    /**
     * Update the specified dimension in storage.
     *
     * @param  int $id
     * @param  \Illuminate\Http\Request $request
     * @param $dimensionId
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request, $dimensionId)
    {
        $assessment = Assessment::findOrFail($id);
        $dimension = Dimension::findOrFail($dimensionId);
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'required',
            'code' => 'required|min:1'
        ]);

        if ($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());

        if (! $data['is_sub'])
            $data['parent'] = 0;

        if ($dimension->isParent() and $data['is_sub'] and $dimension->hasChildren())
        {
            foreach ($dimension->getChildren() as $subdimension)
            {
                $subdimension->parent = $data['parent'];
                $subdimension->save();
            }
        }

        $dimension->update($data);

        return redirect('dashboard/assessments/'.$assessment->id.'/dimensions')->with('success', 'Dimension '.$dimension->name.' updated successfully!');
    }

    /**
     * Remove the specified dimension from storage.
     *
     * @param  int $id
     * @param $dimensionId
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $dimensionId)
    {
        $assessment = Assessment::findOrFail($id);
        $dimension = Dimension::findOrFail($dimensionId);

        if ($dimension->isParent())
            $this->delete_children($dimension);
        $dimension->delete();

        return redirect('dashboard/assessments/'.$assessment->id.'/dimensions')->with('success', 'Dimension '.$dimension->name.' and its Sub-dimensions deleted successfully!');
    }

    /**
     * Delete all children of the specified dimension from storage.
     *
     * @param $dimension
     * @return bool
     */
    private function delete_children($dimension)
    {
        $children = $dimension->getChildren();
        if ($children->isEmpty())
            return false;

        foreach ($children as $child)
            $child->delete();

        return true;
    }
}
