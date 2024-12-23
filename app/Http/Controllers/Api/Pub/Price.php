<?php

namespace App\Http\Controllers\Api\Pub;

use App\Models\Pricepub;
use App\Models\Partnerdeka;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class Price extends Controller
{
    public function index(Request $request)
    {
        $mitra          = $request->mitra;
        $show           = $request->input('show', 5);
        $page           = $request->input('page');
        
        if (empty($mitra)) {
            return response()->json([
                'code'      => 401,
                'message'   => 'data mitra tidak ada',
                'status'    => 'required',
            ]);
        }

        if (empty($page)) {
            return response()->json([
                'code'      => 401,
                'message'   => 'page tidak ada',
                'status'    => 'required',
            ]);
        }

        $list = Pricepub::list_price($mitra);

        $data = $this->paginate($list, $show, $page);
     
        $new_data = [];
        foreach ($data as $value) {
            $new_data[] = $value;
        }

        return response()->json([
            'code'      => 200,
            'message'   => 'success',
            'data'      => $new_data,
        ]);
    }

    public function get_price(Request $request)
    {
        $hours    = $request->hours;
        $personil = $request->personil;
        $mitra    = $request->mitra;


        if (empty($hours)) {
            return response()->json([
                'code'      => 401,
                'message'   => 'hours tidak ada',
                'status'    => 'required',
            ]);
        }

        if (empty($personil)) {
            return response()->json([
                'code'      => 401,
                'message'   => 'personil tidak ada',
                'status'    => 'required',
            ]);
        }

        if (empty($mitra)) {
            return response()->json([
                'code'      => 401,
                'message'   => 'data mitra tidak ada',
                'status'    => 'required',
            ]);
        }

        $get_price = Pricepub::get_price($mitra,$hours,$personil);
        
        return response()->json([
            'code'      => 200,
            'message'   => 'success',
            'data'      => !empty($get_price) ? $get_price : [] ,
        ]);
    }


    public function partnerdeka() {
        return response()->json([
            'code'      => 200,
            'message'   => 'success',
            'data'      => Partnerdeka::get(),
        ]);
    }

    public function paginate($items, $perPage = 10, $page = null, $options = [])

    {

        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);


        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
