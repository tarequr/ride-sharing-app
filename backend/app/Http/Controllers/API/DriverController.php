<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $user->load('driver');

        return $user;
    }

    public function update(Request $request)
    {
        $request->validate([
            'year'          => 'required|numeric|between:2010, 2024',
            'make'          => 'required',
            'model'         => 'required',
            'color'         => 'required',
            'name'          => 'required',
            'license_plate' => 'required',
        ]);

        $user = $request->user();
        $user->update($request->only('name'));

        $user->driver()->updateOrCreate($request->only([
            'year',
            'make',
            'model',
            'color',
            'license_plate',
        ]));

        $user->load('driver');

        return $user;
    }
}
