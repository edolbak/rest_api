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
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    private static string $tinify_api_key;

    public function __construct()
    {
//        return response(json_encode(['$tinify_api_key' => 'sdfsdfsdf']), 200)
//            ->header('Content-Type', 'application/json');

        self::$tinify_api_key = config('app.tinify_api_key');
        Storage::makeDirectory('photos-8005/cropped');
    }


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

        foreach ($users->items() as &$item) {
            $this->formatUserData($item);
        }

        //Return data
        $returnData = [
            'success' => true,
            'total_pages' => $users->lastPage(),
            'total_users' => $users->total(),
            'count' => $count,
            'links' => [
                'next_url' => $users->nextPageUrl() ? $users->nextPageUrl() . '&count=' . $count : null,
                'prev_url' => $users->previousPageUrl() ? $users->previousPageUrl() . '&count=' . $count : null
            ],
            'users' => $users->items()
        ];

        return response(json_encode($returnData), 200)
            ->header('Content-Type', 'application/json');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $jwt = trim($request->header('token'));
        $post = $request->post();
        $photo = null;

        $validationToken = $this->checkValidToken($jwt);

        if (!$validationToken['valid']) {
            $responseData = ['success' => $validationToken['valid'], 'message' => $validationToken['message']];

            return response(json_encode($responseData), 401)
                ->header('Content-Type', 'application/json');
        }


        $validation_errors = [];
        try {
            $request->validate([
                'name' => 'required|min:2|max:60',
                'email' => 'required|email:rfc',
                'position_id' => 'required|integer',
                'phone' => 'required',
                'photo' => 'required|image|mimes:jpg,jpeg|max:3000|dimensions:min_width=70,min_height=70'
            ]);

        } catch (ValidationException $e) {
            $validation_errors = $e->errors();
        }

        if ($validation_errors) {
            $responseData = ['success' => false, 'message' => 'Validation failed', 'fails' => $validation_errors];

            return response(json_encode($responseData), 422)
                ->header('Content-Type', 'application/json');
        }

        if ($this->checkIsEmailPhoneExists($post['email'], $post['phone'])) {
            $responseData = ['success' => false, 'message' => 'User with this phone or email already exist'];

            return response(json_encode($responseData), 409)
                ->header('Content-Type', 'application/json');
        }

        if ($request->hasfile('photo')) {
            $photo = $request->file('photo')->store('photos-8005/origin');
            $path_photo = Storage::path($photo);

            \Tinify\setKey(self::$tinify_api_key);
            $source = \Tinify\fromFile($path_photo);


            $resized_name = str_replace('origin', 'cropped', $photo);
            $resized_path = str_replace('origin', 'cropped', $path_photo);

            $resized = '';
            $resized = $source->resize([
                "method" => "cover",
                "width" => 70,
                "height" => 70
            ])->toFile($resized_path);

            $error = '';
            $id = 0;
            try {
                $id = DB::table('users')->insertGetId([
                    'email' => $post['email'],
                    'name' => $post['name'],
                    'phone' => $post['phone'],
                    'position_id' => $post['position_id'],
                    'photo' => $resized_name,
                    'remember_token' => $jwt,
//                    'email_verified_at' => \DateTime::createFromFormat('Y-m-d H:i:s')->getTimestamp()
                ]);
            } catch (Exception $e) {
                $error = $e->getMessage();
            }


            return response(json_encode([
                'success' => true,
                'user_id' => $id,
                'message' => 'New user successfully registered',
            ]), 201)
                ->header('Content-Type', 'application/json');
        }else{
            $responseData = ['success' => false, 'message' => 'Invalid image of phone field'];

            return response(json_encode($responseData), 401)
                ->header('Content-Type', 'application/json');
        }




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
        $exp = $timestamp + (60 * 40);

        $token = array(
            "iss" => $api_jwt_iss,
            "aud" => $api_jwt_aud,
            "iat" => $iat,
            "exp" => $exp,
            "data" => []
        );


        $jwt = JWT::encode($token, $api_jwt_key, 'HS256');

        $responseData = [
            'success' => true,
            'token' => $jwt
        ];


        return response(json_encode($responseData), 200)
            ->header('Content-Type', 'application/json');
    }

    private function check_expire_token($token): bool
    {
        $api_jwt_key = config('app.api_jwt_key');


        try {
            // Декодирование jwt
            $decoded = JWT::decode($token, new Key($api_jwt_key, 'HS256'));
            return false;
        } catch (Exception $e) {
            return true;
        }
    }

    private function formatUserData(&$userData)
    {
        if (isset($userData->photo)) {
            if (!preg_match('/^http/', $userData->photo, $m)) {
                $userData->photo = asset('storage/' . $userData->photo);
            }
        }

        return $userData;
    }

    private function checkValidToken($token): array
    {
        $response = [
            'valid' => true,
            'message' => ''
        ];

        if ($this->check_expire_token($token)) {
            $response = [
                'valid' => false,
                'message' => 'The token expired.'
            ];
        }

        if ($this->checkIsTokenUsed($token)) {
            $response = [
                'valid' => false,
                'message' => 'The token has been used.'
            ];
        }


        return $response;
    }

    private function checkIsTokenUsed($token): bool
    {
        $user = DB::table('users')
            ->select('users.*')
            ->where('remember_token', $token)->get()->toArray();

        return $user ? true : false;
    }

    private function checkIsEmailPhoneExists($email, $phone): bool
    {
        $ret = false;

        if (DB::table('users')->where('email', $email)->exists()) {
            $ret = true;
        } elseif (DB::table('users')->where('phone', $phone)->exists()) {
            $ret = true;
        }

        return $ret;
    }
}
