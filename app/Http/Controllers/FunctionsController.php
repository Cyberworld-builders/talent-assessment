<?php

namespace App\Http\Controllers;

use App\Article;
use App\Dimension;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class FunctionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($weight_id)
    {
        return view('dashboard.functions.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    public function edit($weight_id, $id)
    {
        $function = new Article(['id' => 1]);

        $dimensionsArray = [];
        foreach (Dimension::all() as $dimension)
            $dimensionsArray[$dimension->id] = $dimension->name;

        return view('dashboard.functions.edit', compact('function', 'dimensionsArray'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function add_dimension(Request $request)
    {
        if (! \Auth::check())
            return false;

        $data = $request->all();

        $dimension = Dimension::find($data['id']);

        return \Response::json($dimension);
    }
}
