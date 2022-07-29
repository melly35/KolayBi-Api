<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;   
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Auth;

use DB;
use App\Models\UserOrders;
use App\Models\UserOrderProducts;
use Validator;
use App\Models\UserCarts;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
      /**
     * Store a newly created resource in storage.
     * @return \Illuminate\Http\Response
     */
    public function getOrders()
    {
        $userId = Auth::user()->id;  
    
        $getOrders = UserOrders::where([ ['userId', $userId] ]) 
        ->get(['id', 'customerName', 'customerAddress']); 

        if($getOrders)
        {   
            return response([
                'success' => true,
                'msg' => 'Data Found',  
                'data' => $getOrders,
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
    public function createOrder(Request $request)
    {
        $userId = Auth::user()->id; 
        $getCarts = UserCarts::join('products', 'user_carts.productId', '=', 'products.id')
            ->where([ ['user_carts.active', 1], ['user_carts.userId', $userId] ])
            ->get(['user_carts.id', 'user_carts.productId', 'user_carts.count', 'products.productPrice']); 
        
        $validator = Validator::make($request->all(), [ 
            'customerName' => 'required',
            'customerAddress' => 'required',            
        ]); 

        if ($validator->fails()) {
            return response([ 
                'success' => false,
                'msg' => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }
        else{ 
            if(count($getCarts) <= 0){
                $response = [
                    'success' => false, 
                    'msg' => 'Not Fount Cart Products',  
                ];
                
                return response($response, Response::HTTP_BAD_REQUEST);
            }
            else{
                $data = $request->all();

                $UserOrders = new UserOrders(); 
                $UserOrders->userId = $userId;
                $UserOrders->customerName = $data['customerName'];
                $UserOrders->customerAddress = $data['customerAddress']; 
                $UserOrders->save();
                $UserOrdersId = $UserOrders->id;

                if($UserOrders){ 
                    foreach ($getCarts as $row) {
                        $UserOrderProducts = new UserOrderProducts(); 
                        $UserOrderProducts->orderId = $UserOrdersId;
                        $UserOrderProducts->productId = $row['productId'];
                        $UserOrderProducts->count = $row['count'];
                        $UserOrderProducts->price = $row['productPrice']; 
                        $UserOrderProducts->save(); 
                    } 
                    $allRemoveCart = UserCarts::where([ ['active', 1], ['userId', $userId] ])->update(['active' => false]); 
                }

                $response = [
                    'success' => true, 
                    'msg' => 'Create Orders Successfully',  
                ];
                
                return response($response, Response::HTTP_OK);
    
            } 
        }
    }

 
}
