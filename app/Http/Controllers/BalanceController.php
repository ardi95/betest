<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Balance;

use Validator;

class BalanceController extends Controller
{
    public function index() {
        $data = Balance::findOrFail(1);

        return response()->json($data, 200);
    }

    public function add(Request $request) {
        $detail = Balance::findOrFail(1);

        $validator = Validator::make($request->all(), [
            'nominal' => 'required',
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

        if ((int)$request->nominal <= 0) {
            $data['errors'][$a] = 'Nominal cannot be less than 0';
            $a++;
            $error = 1;
        }

        if ($error == 1) {
            $data['status'] = 'error';
            return response()->json($data, 400);
        } else {
            DB::transaction(function () use ($request, $detail) {
                $detail->nominal += (int)$request->nominal;
                $detail->status = 'fill';

                $detail->save();
            });

            return response()->json($detail, 200);
        }
    }
}
