<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewsRequest;
use App\Models\News;
use App\Models\CategoryNew;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::paginate(5);
        $categories = CategoryNew::all();
        return view('backend.contents.news.index', compact('news', 'categories'));
    }

    public function create()
    {
        $categories = CategoryNew::select('id', 'name')->get();
        return view('backend.contents.news.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        if ($request->hasFile('image')) {
            $file = $request->image;
            $fileName = $file->getClientOriginalName();
            $path = 'images/news/' . $fileName;
            $data['image'] = $path;
            $file->move('images/news', $fileName);
        }

        $new_news = News::create($data);
        if ($new_news) {
            return redirect()->route('news.index');
        } else {
            return 'Error!!';
        }
    }

    public function edit($id) {
        $news = News::find($id);
        $categories = CategoryNew::select('id', 'name')->get();
        return view('backend.contents.news.edit', compact('news', 'categories'));
    }

    public function update($id, Request $request){
        $news = News::findOrFail($id);
        $data = $request->except(['_token', 'image']);

        // Xử lý file image nếu có upload file mới
        if ($request->hasFile('image')) {
            // Xóa file cũ nếu cần
            if (file_exists(public_path($news->image))) {
                unlink(public_path($news->image));
            }

            // Lưu file mới
            $file = $request->file('image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/news'), $fileName);
            $data['image'] = 'uploads/news/' . $fileName;
        }

        // Cập nhật tin tức
        $updated = $news->update($data);

        if ($updated) {
            return redirect()->route('news.index')->with('success', 'Cập nhật tin tức thành công!');
        } else {
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi cập nhật tin tức!');
        }
    }

    /**
     * @throws Exception
     */
    public function delete($id) {
        $news = News::findOrFail($id);
        $status = $news->delete();
        if ($status == 1) {
            return redirect()->route('news.index');
        } else {
            return 'Error!!';
        }
    }
}
