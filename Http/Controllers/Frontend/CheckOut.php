<?php

namespace App\Http\Controllers\Frontend;

use App\Events\notification;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Http\Requests\CheckingInfoRequest;
use App\Models\Bill;
use App\Models\BillDetail;
use App\Models\Product;

class CheckOut extends Controller
{
    public  function Checkout()
    {
        $data = Cart::content();
        $total = Cart::total();
        return view('frontend.contents.checkout', compact('data', 'total'));
    }
//    public function FinishShopping(CheckingInfoRequest $res)
//    {
//        $data = $res->all();
//        $new_bill = Bill::create($data);
//        $total = 0;
//        foreach (Cart::content() as $product) {
//            $detail = [
//                'bill_id' => $new_bill->id,
//                'product_id' => $product->id,
//                'qty' => $product->qty,
//                'discount' => $product->options->discount,
//                'total' => $product->price * $product->qty
//            ];
//            Billdetail::create($detail);
//            $total += $product->price *$product->qty;
//        }
//        // dd($total);
//        Bill::where('id', $new_bill->id)->update(['total' => $total]);
//        if ($new_bill) {
//            $notification = [
//                'title' => $new_bill->Name . ' have a new order',
//                'link' => route('order.detail', $new_bill->id),
//            ];
//            Cart::destroy();
//            event(new notification($notification));
//
//            return redirect()->route('product');
//        } else {
//            return 'Error!!';
//        }
//    }
    public function FinishShopping(CheckingInfoRequest $res)
    {
        $data = $res->all();
        $new_bill = Bill::create($data);
        $total = 0;

        foreach (Cart::content() as $product) {
            // Thêm chi tiết hóa đơn
            $detail = [
                'bill_id' => $new_bill->id,
                'product_id' => $product->id,
                'qty' => $product->qty,
                'discount' => $product->options->discount,
                'total' => $product->price * $product->qty
            ];
            Billdetail::create($detail);

            // Cập nhật tổng tiền
            $total += $product->price * $product->qty;

            // Cập nhật số lượng sản phẩm trong kho
            $product_in_db = Product::find($product->id);
            if ($product_in_db) {
                $product_in_db->qty -= $product->qty; // Giảm số lượng sản phẩm trong kho
                $product_in_db->save();
            }
        }

        // Cập nhật tổng số tiền của hóa đơn
        Bill::where('id', $new_bill->id)->update(['total' => $total]);

        if ($new_bill) {
            // Tạo thông báo và gửi sự kiện
            $notification = [
                'title' => $new_bill->Name . ' have a new order',
                'link' => route('order.detail', $new_bill->id),
            ];
            Cart::destroy();
            event(new notification($notification));

            // Chuyển hướng người dùng về trang sản phẩm
            return redirect()->route('product');
        } else {
            return 'Error!!';
        }
    }

}
