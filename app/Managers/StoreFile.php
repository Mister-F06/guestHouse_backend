<?php

namespace App\Managers;

use Illuminate\Support\Str;

class StoreFile{

    public static function addFile($file , $mediaCollectionName , $element)
    {
        $extension = $file->extension();
        $file_name = Str::slug(explode('.' , $file->getClientOriginalName())[0], '-');
        $element->addMedia($file->getPathname())
                    ->usingName($file_name)
                    ->usingFileName($file->getClientOriginalName())
                    ->toMediaCollection($mediaCollectionName);
    }

    public static function addFileFromUrl($picture , $mediaCollectionName , $element)
    {
        $element->addMedia($picture->getPath())
                ->preservingOriginal()
                ->toMediaCollection($mediaCollectionName);
    }
}