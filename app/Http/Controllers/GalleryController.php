<?php

namespace Modules\Portail\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Portail\Models\Photo;

class GalleryController extends Controller
{
    public function index()
    {
        return view('portail::gallery.index');
    }

    public function list()
    {
        $photos = Photo::where('user_id', Auth::id())
            ->where('is_visible_to_client', true)
            ->latest()
            ->paginate(12);

        return response()->json($photos);
    }

    public function show(Photo $photo)
    {
        abort_if($photo->user_id !== Auth::id(), 403);
        return response()->json($photo);
    }
}
