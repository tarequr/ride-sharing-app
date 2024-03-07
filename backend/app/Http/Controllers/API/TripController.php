<?php

namespace App\Http\Controllers\API;

use App\Events\TripAccepted;
use App\Events\TripCreated;
use App\Events\TripEnded;
use App\Events\TripLocationUpdated;
use App\Events\TripStarted;
use App\Http\Controllers\Controller;
use App\Models\Trip;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'origin' => 'required',
            'destination' => 'required',
            'destination_name' => 'required'
        ]);

        $trip = $request->user()->trips()->create($request->only([
            'origin',
            'destination',
            'destination_name'
        ]));

        TripCreated::dispatch($trip, $request->user());

        return $trip;
    }

    public function show(Request $request, Trip $trip)
    {
        if ($trip->user->id == $request->user()->id) {
            return $trip;
        }

        if ($trip->driver && $request->user()->driver) {
            if ($trip->driver->id == $request->user()->driver->id) {
                return $trip;
            }
        }

        return response()->json(['message' => 'Cannot find this trip.'], 404);
    }

    public function accept(Request $request, Trip $trip)
    {
        $request->validate([
            'driver_location' => 'required'
        ]);

        $trip->update([
            'driver_id' => $request->user()->id,
            'driver_location' => $request->driver_location
        ]);

        $trip->load('driver.user');

        TripAccepted::dispatch($trip, $trip->user);

        // Reference
            // -> https://github.com/soketi/soketi
            // -> https://github.com/beyondcode/laravel-websockets
            // -> https://beyondco.de/docs/laravel-websockets/getting-started/introduction/

            // php artisan websockets:serve

        return $trip;
    }

    public function start(Request $request, Trip $trip)
    {
        $trip->update([
            'is_started' => true
        ]);

        $trip->load('driver.user');

        TripStarted::dispatch($trip, $request->user());

        return $trip;
    }

    public function end(Request $request, Trip $trip)
    {
        $trip->update([
            'is_complete' => true
        ]);

        $trip->load('driver.user');

        TripEnded::dispatch($trip, $request->user());

        return $trip;
    }

    public function location(Request $request, Trip $trip)
    {
        $request->validate([
            'driver_location' => 'required'
        ]);

        $trip->update([
            'driver_location' => $request->driver_location
        ]);

        TripLocationUpdated::dispatch($trip, $request->user());

        $trip->load('driver.user');

        return $trip;
    }
}
