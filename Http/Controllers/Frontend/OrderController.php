<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function showOrderStatus()
    {
        // Kiểm tra nếu người dùng đã đăng nhập
        if (Auth::check()) {
            // Lấy tất cả đơn hàng của người dùng
            $orders = Bill::where('id_user', Auth::id())->get();
            return view('frontend.contents.OrderStatus', compact('orders'));
        } else {
            return redirect()->route('/')->with('error', 'Vui lòng đăng nhập để xem trạng thái đơn hàng');
        }
    }
    public function cancelOrder($orderId)
    {
        // Kiểm tra nếu người dùng đã đăng nhập và đơn hàng thuộc về người dùng đó
        $order = Bill::where('id', $orderId)->where('user_id', Auth::id())->first();

        if (!$order) {
            return redirect()->route('order.status')->with('error', 'Đơn hàng không tồn tại hoặc bạn không có quyền hủy đơn hàng này');
        }

        // Kiểm tra trạng thái đơn hàng, chỉ cho phép hủy khi đơn hàng chưa giao
        if ($order->status == 3) {
            return redirect()->route('order.status')->with('error', 'Không thể hủy đơn hàng đã giao');
        }

        // Cập nhật trạng thái đơn hàng thành "Đã hủy"
        $order->status = 4;
        $order->save();

        // Thông báo cho người dùng
        return redirect()->route('order.status')->with('success', 'Đơn hàng đã được hủy thành công');
    }

}

