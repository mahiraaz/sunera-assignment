<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class OrderController extends Controller
{
    
    public function createOrder(Request $request)
    {

        $input = $request->all();
        $response['success'] = '0';

        $validator = Validator::make($request->all(), [
            'item_name' => 'required|max:200',
            'item_price' => 'required',
            'item_quantity' => 'required|max:20',
            'item_type' => 'required|max:20',
            'item_color' => 'required|max:20',
            'order_by' => 'required|numeric|min:10',
            'order_time_limit' => 'required',
        ]);

        if ($validator->fails()) {
            $response['error'] = $validator->getMessageBag();
        } else {
            $order_no = $this->getNextOrderNumber();
            
            $current = Carbon::now();
            $expted_processingtime = $current->addDays(2);

            if(!empty($input['order_creation_time'])){
                $current = $input['order_creation_time'];
            }

            $order = new Order;
            $order->order_no = $order_no;
            $order->item_name = $input['item_name'];
            $order->item_price = $input['item_price'];
            $order->item_quantity = $input['item_quantity'];
            $order->item_type = $input['item_type'];
            $order->item_category = $input['item_category'];
            $order->item_color = $input['item_color'];
            $order->order_by = $input['order_by'];
            $order->order_creation_time = $current;
            $order->order_time_limit = $input['order_time_limit'];
            $order->order_expected_processing_time = $expted_processingtime;
            $order->save();

            $insertedId = $order->order_no;
            $response['success'] = '1';
            $response['order_id'] = $insertedId;
            $response['order_by'] = $input['order_by'];
            $response['order_expected_processing_time'] = $expted_processingtime;
            $response['order_status'] = 'Created';
        }

        

        return response()->json($response);
    }

    public function processOrder(Request $request)
    {
        $input = $request->all();
        $response['success'] = '0';

        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            $response['error'] = $validator->getMessageBag();
        } else {

            $current = Carbon::now();

            $order = Order::where('order_no', $input['order_id'])->first();

            $order = Order::find($order->order_id);
            $order->order_status = '2';
            $order->order_processing_time = $current;
            $order->save();

            $response['order_id'] = $input['order_id'];
            $response['order_status'] = 'Processing';
            $response['order_processing_time'] = $current;
            
            // return $order;

        }

        return response()->json($response);
    }

    public function confirmOrder(Request $request)
    {
        $input = $request->all();
        $response['success'] = '0';

        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            $response['error'] = $validator->getMessageBag();
        } else {

            $current = Carbon::now();
            $order_expected_date = $current->addDays(5);

            $order = Order::where('order_no', $input['order_id'])->first();
            $invoice_no = $this->getInvoiceNumber($order->order_id);

            $order = Order::find($order->order_id);
            $order->order_status = '3';
            $order->order_expected_date = $order_expected_date;
            $order->invoice_no = $invoice_no;
            $order->save();

            $response['order_id'] = $input['order_id'];
            $response['order_status'] = 'Completed';
            $response['order_expected_date'] = $order_expected_date;
            $response['invoice_no'] = $invoice_no;
            
            // return $order;
        }

        return response()->json($response);
    }

    public function getNextOrderNumber()
    {
        $lastOrder = Order::orderBy('created_at', 'desc')->first();

        if ( ! $lastOrder )
            $number = 0;
        else    
            $number = $lastOrder->order_id;

        return 'ORD' . sprintf('%013d', intval($number) + 1);
    }

    public function getInvoiceNumber($id)
    {  
        $number = $id;
        return 'INV' . sprintf('%013d', intval($number));
    }
}