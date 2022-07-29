<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use Illuminate\Support\Facades\Auth;

use App\Models\Product;
use App\Models\SubCategories;
use Illuminate\Http\Request;
use Validator; 
use Symfony\Component\HttpFoundation\Response;


class ProductsController extends Controller
{ 
    /** 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    */ 
    public function products(Request $request)
    {
          
        $userId = Auth::user()->id;
        $query = Product::query()->where('userId', '=', $userId);

        if($request->filled('q') && strlen($request->q) >= 3) //Barcode and Name 
        {
            $query->where('productName', 'like', '%'.$request->q.'%')
                ->orWhere('productBarcode', 'like', '%'.$request->q.'%');
        } 

        if($request->filled('prc')) //price
        {
            list($min, $max) = explode("-", $request->prc);
            $query->where('productPrice', '>=', $min)
                  ->where('productPrice', '<=', $max);
        }

        if($request->filled('cat')) //categories
        {
            $query->where('mainCategoryId', '=', $request->cat); 
        }

        // $limit = 5;
        // if($request->p == 1)
        //     $offset = 0;
        // else
        //     $offset = $request->p * $limit;

        return response([ 
            'success' => true,
            'msg' => 'Data Found',
            'data' => $query->get(),
        ], Response::HTTP_OK);
      
    }

    /**
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function addProduct(Request $request)
    {
        $userId = Auth::user()->id;

        $validator = Validator::make($request->all(), [
            'productBarcode' => 'required|max:15|unique:products',
            'productName' => 'required||max:255', 
            'productPrice' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'mainCategoryId' => 'required',
            'subCategoryId' => 'required',
            //'productImage' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:4096',
        ]); 

        if ($validator->fails()) {
            return response([ 
                'success' => false,
                'msg' => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }
        else { 
            $Product = new Product;

            $data = $request->all();
            //$image_path = $request->file('productImage')->store('image', 'public');

            $Product->userId = $userId;
            $Product->productBarcode = $data['productBarcode'];
            $Product->productName = $data['productName'];
            $Product->productPrice = $data['productPrice'];
            $Product->mainCategoryId = $data['mainCategoryId'];
            $Product->subCategoryId = $data['subCategoryId'];
            $Product->productDesc = $data['productDesc'];
            //$Product->productImage = $image_path;
            
            $Product->save();
 
            $response = [
                'success' => true, 
                'msg' => 'Add Product Successfully',
                'user' => $Product, 
            ];
            
            return response($response, Response::HTTP_OK);
        } 
    }

    public function store(Request $request)
    {
        $data = $request->all();
        return response($data, Response::HTTP_OK);
    }

    public function getCategories()
    { 
        $query = Categories::query()->get(['id', 'categoryName']); 
 
        return response([ 
            'success' => true,
            'msg' => 'Data Found',
            'data' => $query,
        ], Response::HTTP_OK);
      
    }


    /**
    * @param  \Illuminate\Http\Request  $request
    * @param  string  $mainCatId
    * @return \Illuminate\Http\Response
    */
    public function getSubCategories(Request $request)
    { 
        $query = SubCategories::query()->get(["id", "mainCategoryId", "subCategoryName"]);
 
        return response([ 
            'success' => true,
            'msg' => 'Data Found',
            'data' => $query,
        ], Response::HTTP_OK);
      
    }
}
