<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Laravel\Ui\Presets\React;

class ProductController extends Controller
{
    public function product(Request $request, $id = null)
    {
        // Nếu có category_id, lấy sản phẩm theo category_id, nếu không lấy tất cả sản phẩm
        if ($id) {
            $products = Product::where('product_category_id', $id)->paginate(8);
        } else {
            $products = Product::paginate(8); // Lấy tất cả sản phẩm
        }

        $categories = ProductCategory::select('id', 'name')->get();
        return view('frontend.contents.shopping', compact('products', 'categories'));
    }


    public function detail($id)
    {
        $product = Product::find($id);
        $images = $product->images;
        $categories = ProductCategory::select('id', 'name')->get();
        $relatedProduct = Product::where('product_category_id', $product->product_category_id)->get();
        return view('frontend.contents.product_detail', compact('product', 'images', 'categories', 'relatedProduct', 'id'));
    }

    public function renderHtml($array)
    {
        $html = '';
        foreach ($array as $product) {
            $html .= '
            <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                <div class="product-wrap mb-35" data-aos="fade-up" data-aos-delay="200">
                    <div class="product-img img-zoom mb-25">
                        <a href="' . route('detail', $product->id) . '">
                            <img src="' . asset($product->thumbnail) . '" alt="">
                        </a>
                        <div class="product-badge badge-top badge-right badge-pink">
                            <span>-' . $product->promotion->percent . '%</span>
                        </div>

                        <div class="product-action-2-wrap">
                            <a href="#" onclick="return false;" data-url_addcart="' . route('addtocart') . '" id="' . $product->id . '" class="product-action-btn-2 add-cart" title="Add to cart" ><i class="pe-7s-cart"></i> Add to cart</a>
                        </div>
                    </div>
                    <div class="product-content">
                        <h3><a href="' . route('detail', $product->id) . '">' . $product->name . '</a></h3>
                        <div class="product-price"> ';
            if ($product->promotion->percent == 0) {
                $html .= '<span class="old-price-current"> ' . number_format($product->price, 0, '', '.') . ' đ </span>';
            } else {
                $html .= '<span class="old-price"> ' . number_format($product->price, 0, '', '.') . ' đ </span>
                                            <span class="new-price"> ' . number_format($product->price - ($product->price * $product->promotion->percent) / 100, 0, '', '.') . ' đ </span>';
            }

            $html .= '
                        </div>
                    </div>
                </div>
            </div> ';
        }
        return $html;
    }

    public function renderProductByCategory(Request $request)
    {
        $categoryId = $request->id;

        // Kiểm tra nếu chọn "Tất cả sản phẩm"
        if ($categoryId != 'all') {
            // Nếu có chọn category, lấy sản phẩm theo category
            $products = Product::where('product_category_id', $categoryId)->paginate(10);
        } else {
            // Nếu chọn "Tất cả sản phẩm"
            $products = Product::paginate(10);
        }

        // Render lại HTML của sản phẩm
        $html = $this->renderHtml($products);

        // Trả về HTML
        return $html;
    }


    public function searchProduct(Request $request)
    {
        $keyword = $request->input('keyword');
        $products = Product::where('name', 'LIKE', "%$keyword%")->get();
        return response()->json($products);
    }
}
