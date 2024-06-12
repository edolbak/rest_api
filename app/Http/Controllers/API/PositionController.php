<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Position;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\RecordsNotFoundException;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $positions = Position::all();

            if($positions->isEmpty()){
                $responseData = ['success' => false, 'message' => 'Positions not found'];
                return response(json_encode($responseData), 404)
                    ->header('Content-Type', 'application/json');
            }

            $responseData = ['success' => true, 'positions' => $positions];
            return response(json_encode($responseData), 200)
                ->header('Content-Type', 'application/json');

        }catch (\Exception $e){
            $responseData = ['success' => false, 'message' => 'Positions not found'];
            return response(json_encode($responseData), 422)
                ->header('Content-Type', 'application/json');
        }

        $responseData = ['success' => false, 'message' => 'Invalid image of phone field'];

        return response(json_encode($positions), 401)
            ->header('Content-Type', 'application/json');

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Position $position)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Position $position)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Position $position)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Position $position)
    {
        //
    }
}
