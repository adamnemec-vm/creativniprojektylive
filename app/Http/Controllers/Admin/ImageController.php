<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Services\ImageStorage;
use Illuminate\Support\Facades\Gate;

class ImageController extends Controller
{
    public function destroy(Image $image, ImageStorage $images)
    {
        Gate::authorize('update', $image->post);

        $image->delete();
        $images->delete($image->image_path);

        return request()->expectsJson()
            ? response()->noContent()
            : back()->with('success', 'Obrázek byl úspěšně smazán.');
    }
}
