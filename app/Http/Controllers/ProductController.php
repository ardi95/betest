<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Product;

class ProductController extends Controller
{
    public function index(Request $request) {
        $data = Product::orderBy('id', 'asc')->paginate($request->per_page);

        return response()->json($data, 200);
    }
}
