<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Event;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = JWTAuth::parseToken()->authenticate();
        return $user->events()->orderBy('occurrence', 'asc')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'occurrence' => 'required|date',
            'description' => 'nullable|string',
        ]);
        $user = JWTAuth::parseToken()->authenticate();
        $event = $user->events()->create($data);

        return response()->json($event, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $event = $user->events()->where('id', $id)->firstOrFail();

        $data = $request->validate([
            'description' => 'nullable|string',
        ]);

        $event->update($data);

        return response()->json($event);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event, $id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $event = $user->events()->where('id', $id)->firstOrFail();

        $event->delete();

        return response()->json(null, 204);
    }

}
