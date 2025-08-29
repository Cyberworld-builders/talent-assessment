<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Industry;
use Illuminate\Support\Facades\Validator;

class IndustriesController extends Controller
{
    public function index()
    {
        $industries = Industry::all()->sortBy('name');
        return view('dashboard.industries.index', compact('industries'));
    }

    public function create()
    {
        return view('dashboard.industries.create');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $industry = new Industry($data);
        $industry->save();

        return redirect('dashboard/industries')
            ->with('success', 'Industry '.$industry->name.' created successfully!');
    }

    public function edit($id)
    {
        $industry = Industry::findOrFail($id);
        return view('dashboard.industries.edit', compact('industry'));
    }

    public function update(Request $request, $id)
    {
        $industry = Industry::findOrFail($id);
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required|unique:industries,name,'.$industry->id,
        ]);

        if ($validator->fails())
            return redirect()->back()->withErrors($validator->errors());

        $industry->update($data);

        return redirect('dashboard/industries')
            ->with('success', 'Industry '.$industry->name.' updated successfully!');
    }

    public function destroy($id)
    {
        $industry = Industry::findOrFail($id);
        $industry->delete();

        return redirect('dashboard/industries')
            ->with('success', 'Industry '.$industry->name.' deleted successfully!');
    }
}

