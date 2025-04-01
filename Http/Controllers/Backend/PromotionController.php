<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class PromotionController extends Controller
{
    public function index() {
        $promotions = Promotion::paginate(5);
        return view('backend.contents.promotion.index', compact('promotions'));
    }
    public function create() {
        return view('backend.contents.promotion.create');
    }
    public function submitcreate(Request $res) {
        $data = $res->all();
        $new_promo = Promotion::create($data);
        if ($new_promo) {
            return Redirect::to('admin/promotion/index');
        } else {
            return 'Error!!';
        }
    }
    public function edit($id) {
        $product = Promotion::find($id);
        $categories = Promotion::select('id', 'name')->get();
        $promotions = Promotion::select('id', 'name')->get();
        return view('backend.contents.product.edit', compact('product', 'categories', 'promotions'));
    }

    /**
     * @throws \Exception
     */
    public function delete($id) {
        $promotion = Promotion::findOrFail($id);
        $status = $promotion->delete();
        if ($status == 1) {
            return redirect()->route('promotion.index');
        } else {
            return 'Error!!';
        }
    }
}
