<?php

namespace App\Http\Controllers\Api;
  
use App\Http\Controllers\Controller;   
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Auth;
use App\Models\UserCarts;
use Validator; 
use DB;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getCart(Request $request)
    {
        $userId = Auth::user()->id;   
    
        $getCart = UserCarts::
        join('products', 'productId', '=', 'products.id')
        ->where([ ['active', 1], ['user_carts.userId', $userId] ])
        ->get(['user_carts.id', 'user_carts.productId', 'user_carts.count', 'products.productName']); 

        if($getCart)
        { 
            return response([
                'success' => true,
                'msg' => 'Data Found', 
                'public_path' => asset('storage/'),
                'data' => $getCart,
            ], Response::HTTP_OK);
        } 
        else
        {
            return response(['success' => false, 'msg' => 'Data Not Found'], Response::HTTP_NOT_FOUND);
        } 
 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addCart(Request $request)
    {
        $userId = Auth::user()->id; 
        
        $validator = Validator::make($request->all(), [ 
            'productId' => 'required', 
        ]); 

        if ($validator->fails()) {
            return response([ 
                'success' => false,
                'msg' => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }
        else{ 

            $data = $request->all();

            $UserCart = UserCarts::updateOrCreate([
                'userId' => $userId,
                'productId' => $data['productId'],
                'active' => true
            ], [
                'userId' => $userId,
                'productId' => $data['productId'],
                'count' => DB::raw('count + ' . $data['count']),
                'active' => true
            ])->save();
 
            $response = [
                'success' => true, 
                'msg' => 'Add Cart Successfully',
                'user' => $UserCart, 
            ];
            
            return response($response, Response::HTTP_OK);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function removeCart(Request $request)
    {
        $userId = Auth::user()->id;  

        $validator = Validator::make($request->all(), [ 
            'productId' => 'required', 
        ]); 

        if ($validator->fails()) {
            return response([ 
                'success' => false,
                'msg' => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }
        else{ 

            $data = $request->all();
            $removeCart = UserCarts::where([ ['active', 1], ['userId', $userId], ['productId', $data['productId']] ])->update(['active' => false]); 

            if($removeCart)
            { 
                return response([
                    'success' => true,
                    'msg' => 'Remove Cart',   
                ], Response::HTTP_OK);
            } 
            else
            {
                return response(['success' => false, 'msg' => 'Data Not Found'], Response::HTTP_NOT_FOUND);
            } 

        }
    
        
 
    }
    
}
