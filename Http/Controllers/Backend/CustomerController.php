<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index() {
        $customers = User::paginate(5);
        return view('backend.contents.customer.index', compact('customers'));
    }
    public function delete($id) {
        $customer = User::findOrFail($id);
        $status = $customer->delete();
        if ($status == 1) {
            return redirect()->route('customer.index');
        } else {
            return 'Error!!';
        }
    }
    public function create() {
        return view('backend.contents.customer.create');
    }
    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        // Sử dụng mass assignment với $fillable
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('customer.index')->with('success', 'Thêm khách hàng mới thành công!');
    }

    public function update($id, Request $request) {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$id,
            'password' => 'nullable|string|min:8',
        ]);

        // Chuẩn bị dữ liệu cập nhật
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // Chỉ cập nhật mật khẩu nếu được cung cấp
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        // Cập nhật dữ liệu
        $user->update($userData);

        return redirect()->route('customer.index')->with('success', 'Cập nhật khách hàng thành công!');
    }


}
