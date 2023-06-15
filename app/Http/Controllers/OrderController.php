<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Balance;
use App\Product;

use Validator;

class OrderController extends Controller
{
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'orders' => 'array|min:1',
            //'note' => 'required'
        ]);

        $error = 0;
        $a = 0;
        $data = array();
        $data['errors'] = [];

        if ($validator->fails()) {
            $errors = $validator->errors()->getMessages();

            foreach ($errors as $value) {
                $data['errors'][$a] = $value[0];
                $a++;
            }

            $error = 1;
        }

        $totalPrice = 0;

        //echo "<pre>";
        //var_dump($request->orders);
        //echo "</pre>";
        //die();

        foreach ($request->orders as $order) {
            $product = Product::findOrFail($order['id']);

            $totalPrice += $order['price'];

            if ($product->qty < $order['qty']) {
                $data['errors'][$a] = $product->name .' stock is insufficient with the amount ordered';
                $a++;
                $error = 1;
            }
        }

        $balance = Balance::findOrFail(1);
        if ($balance->nominal < $totalPrice) {
            $data['errors'][$a] = 'Balance cannot be less than the total payment';
            $a++;
            $error = 1;
        }

        if ($error == 1) {
            $data['status'] = 'error';
            return response()->json($data, 400);
        } else {
            $data['balance'] = null;
            $data['returns'] = null;
            return response()->json(DB::transaction(function () use ($request, $data) {
                $totalPrice = 0;

                foreach ($request->orders as $order2) {
                    $product = Product::findOrFail($order2['id']);

                    $totalPrice += $order2['price'];

                    $product->qty -= $order2['qty'];
                    $product->save();
                }

                $balance = Balance::findOrFail(1);

                $returns = $balance->nominal - $totalPrice;

                $balance->nominal = 0;
                $balance->status = 'empty';

                $balance->save();

                $data['balance'] = $balance;
                $data['returns'] = $returns;

                return $data;
            }), 200);

        }
    }
}
