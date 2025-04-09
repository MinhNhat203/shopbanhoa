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
    public function cancelOrder(Request $request, $orderId)
    {
        $order = Bill::where('id', $orderId)->where('id_user', Auth::id())->first();

        if (!$order) {
            return redirect()->route('order.status')->with('error', 'Đơn hàng không tồn tại hoặc bạn không có quyền hủy');
        }

        // Kiểm tra trạng thái đơn hàng
        if ($order->Status != 0) {
            return redirect()->route('order.status')->with('error', 'Không thể hủy đơn hàng');
        }

        // Lấy giá trị status từ request, nếu không có thì mặc định là 2
        $status = $request->input('status', 2);

        // Cập nhật trực tiếp
        Bill::where('id', $orderId)->update(['Status' => $status]);

        return redirect()->route('order.status')->with('success', 'Đơn hàng đã được hủy thành công');
    }

}

