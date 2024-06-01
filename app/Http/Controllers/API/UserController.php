<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $count = intval($request->get('count')) ?: 6;
        $page = intval($request->get('page')) ?: 1;

        $users = DB::table('users')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.phone',
                'users.position_id',
                'positions.name as position',
                'users.photo',
                (DB::raw('UNIX_TIMESTAMP(users.created_at) as registration_timestamp'))
            )
            ->orderBy('id')
            ->paginate(perPage: $count, page: $page);

        //Return data
        $returnData = [
            'success'=>true,
            'total_pages'=>$users->lastPage(),
            'total_users'=>$users->total(),
            'count'=>$count,
            'links'=>[
                'next_url'=>$users->nextPageUrl()?$users->nextPageUrl().'&count='.$count:null,
                'prev_url'=>$users->previousPageUrl()?$users->previousPageUrl().'&count='.$count:null
            ],
            'users'=>$users->items()
        ];

        return response(json_encode($returnData), 200)
            ->header('Content-Type', 'application/json');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $jwt = $request->header('token');
        $data = $request->post();

        $responseData = ['success'=>true, 'message'=>'Ok'];


        $isValidToken = $this->validate_token($jwt);
        if(!$isValidToken) {
            $responseData = ['success'=>false, 'message'=>'The token expired.'];

            return response(json_encode($responseData), 401)
                ->header('Content-Type', 'application/json');
        }

        return response(json_encode($responseData), 200)
            ->header('Content-Type', 'application/json');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // TODO-vardump VAR_DUMP
        die(var_dump(__CLASS__, '===>', __METHOD__));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function token(): Response
    {
        $timestamp = now()->getTimestamp();

        $api_jwt_key = config('app.api_jwt_key');
        $api_jwt_iss = config('app.api_jwt_iss');
        $api_jwt_aud = config('app.api_jwt_aud');
        $iat = $timestamp;
        $exp = $timestamp+(60*40);

        $token = array(
            "iss" => $api_jwt_iss,
            "aud" => $api_jwt_aud,
            "iat" => $iat,
            "exp" => $exp,
            "data" => []
        );


        $jwt = JWT::encode($token, $api_jwt_key, 'HS256');

        $responseData = [
            'success'=>true,
            'token'=>$jwt
        ];


        return response(json_encode($responseData), 200)
            ->header('Content-Type', 'application/json');
    }

    private function validate_token($token): Exception|bool
    {
        $api_jwt_key = config('app.api_jwt_key');


        try {
            // Декодирование jwt
            $decoded = JWT::decode($token, new Key($api_jwt_key, 'HS256'));
            return true;
        }catch (Exception $e) {
            return false;
        }
    }
}
