<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(){
        return view('orders', [
            'data' => Order::AllOrders()
        ]);
    }


    public function order($id){
        return view('orderdetail', [
            'data' => Order::Order($id)
        ]);
    }

    public function update(Request $request, $id)
    {
        try
        {
            $id =$request->input('pk');
            $field = $request->input('name');
            $value =$request->input('value');


//            $order = Order::findOrFail($id);
//            $order->{$field} = $value;
                //...и по остальным полям формы
//            $order->save();

        }
        catch (Exception $e)
        {
            return response($e->intl_get_error_message(), 400);
        }
        return response('',200);

    }
}
