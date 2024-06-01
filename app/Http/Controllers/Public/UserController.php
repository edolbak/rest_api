<?php

namespace App\Http\Controllers\Public;

use App\Classes\CustomPaginator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Http;


class UserController extends Controller
{
    private string|null $api_host;

    public function __construct()
    {
        $this->api_host = config('app.api_host');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $count = intval($request->get('count')) ?: 6;
        $page = intval($request->get('page')) ?: 1;

//        $ses = $request->session()->all();
//        dd($ses);


        $result = Http::timeout(10)
            ->get($this->api_host.'/api/v1/users?page='.$page.'&count='.$count);

    //        $curl_post_data = [
//            "count" => $count,
//            "page" => "$page",
//        ];
//        $url =$this->api_host.'/api/v1/users';
//        $data = json_encode($curl_post_data);
//        $ch=curl_init();
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
//        $curl_response = curl_exec($ch);
//
//        $result = json_decode($curl_response,true);

        $usersArray = [];
        $links = [];
        $total_pages = 0;
        $total_users = 0;

        if($result->status() == 200 && $result->json()){
            $data = $result->json();
            $usersArray = array_key_exists('users', $data)?$data['users']:[];

            if(array_key_exists('links', $data)){
                $links['next_url'] = array_key_exists('next_url', $data['links'])?
                    $this->convertPaginatorLink($data['links']['next_url']):null;
                $links['prev_url'] = array_key_exists('prev_url', $data['links'])?
                    $this->convertPaginatorLink($data['links']['prev_url']):null;
            }

            $total_pages = array_key_exists('total_pages', $data)?$data['total_pages']:0;
            $total_users = array_key_exists('total_users', $data)?$data['total_users']:0;
        }

        $paginator = new CustomPaginator(
            $usersArray,
            $count,
            $page,
            ['links'=>$links, 'total_pages'=>$total_pages, 'total_users'=>$total_users]
        );

        return view('auth.list', [
            'users' => $usersArray,
            'paginator' => $paginator
        ]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('auth.register');
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

    private function convertPaginatorLink($link)
    {
        $url_parsed = parse_url($link);
        if($url_parsed && array_key_exists('query', $url_parsed)){
            $link = route('list', explode('&', $url_parsed['query']));
        }

        return $link;
    }


}
