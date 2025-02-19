<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function destroy(Image $image)
    {
        Storage::disk('public')->delete($image->image_path);
        $image->delete();
        
        return back()->with('success', 'Obrázek byl úspěšně smazán.');
    }
} 