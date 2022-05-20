<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use KingFlamez\Rave\Facades\Rave as Flutterwave;

class PaymentController extends Controller
{
    public function createpayment(Request $request) {
        $request->validate([
            "name" => "required|string",
            "amount" => "required|integer",
            "email" => "required|email"
        ]);
        try {
            $reference = Flutterwave::generateReference();
            $data = [
                'amount' => $request->amount,
                'email' => $request->email,
                'tx_ref' => $reference,
                'narration' => $request->name
            ];
            $bankDetails = Flutterwave::payments()->nigeriaBankTransfer($data);
            if ($bankDetails['status'] === 'success') {
                return $bankDetails;
            }
            if ($bankDetails["status"] != 'success') {
                return response()->json([
                    "message" => "Error occured.",
                    "status" => "error"
                ], 400);
            }
        } catch (\Throwable $th) {
            return response()->json([
                "message" => "Error occured.",
                "status" => "error"
            ], 400);
        }   
    }


    public function webhook(Request $request) {
        $verified = Flutterwave::verifyWebhook();
        if ($verified && $request->event == 'charge.completed' && $request->data->status == 'successful') {
            $verificationData = Flutterwave::verifyPayment($request->data['id']);
            if ($verificationData['status'] === 'success') {
            // process for successful charge
                Log::success($verified);
            }
        }

    }

}
