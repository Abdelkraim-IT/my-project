<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

trait FileUploader
{
public function uploadFile($request, $data = null, $name = 'default', $inputName = 'image')
{
    $requestFile = $request->file($inputName);
    try {
        if ($requestFile) {
            $file = $requestFile;

            $fileName = $data . '-' . $name . '.' . $file->getClientOriginalExtension();
            $file->storeAs($name, $fileName); // Use `storeAs` for public storage

            // Use `asset` for web-accessible URL (assuming public storage)
            $url = asset($name . '/' . $fileName);

            return $url;
        } else {
            // Handle scenario where no file is uploaded (e.g., return default image URL)
            return 'default_image_url.jpg'; // Example placeholder
        }
    } catch (\Throwable $th) {
        report($th);
        return $th->getMessage();
    }
}
public function handleBase64Image($base64Image)
{
    $decodedImage = null;

    if ($base64Image) {
        // Decode image data
        $decodedImage = base64_decode($base64Image);
    }

    // Handle saving the Base64 image
    $filename = uniqid() . '.jpg'; // Generate unique filename
    $imageType = preg_replace('/^data:image\/(\w+);base64,/', '', $base64Image); // Extract image type
    $imagePath = 'public_html/image/' . $filename; // Path to save the image

    // Create the 'image' directory if it doesn't exist
    if (!File::exists('public_html/image')) {
        File::makeDirectory('public_html/image', 0755, true);
    }

    File::put($imagePath, $decodedImage); // Save decoded image
    $imageUrl = asset('public_html/image/' . $filename); // Get web-accessible URL

    return $imageUrl;
}
public function handleBase64File($base64Url)
{
    $decodedFile = null;

    if ($base64Url) {
        // فك تشفير بيانات الملف
        $decodedFile = base64_decode($base64Url);
    } else {
        // في حالة عدم وجود البيانات base64
        return null;
    }

    $filename = uniqid() . '.pdf'; // إنشاء اسم ملف فريد
    $fileType = 'application/pdf';
    $filePath = 'public_html/pdf/' . $filename;

    if (!File::exists('public_html/pdf')) {
        File::makeDirectory('public_html/pdf', 0755, true);
    }

    File::put($filePath, $decodedFile);
    $fileUrl = asset('public_html/pdf/' . $filename); // الحصول على عنوان URL القابل للوصول على الويب

    return $fileUrl;
}

    // delete file
    public function deleteFile($fileName = 'files')
    {
        try {
            if ($fileName) {
                Storage::delete('public/files/' . $fileName);
            }

            return true;
        } catch (\Throwable $th) {
            report($th);

            return $th->getMessage();
        }
    }

    public function uploadImage($request, $data, $name, $inputName = 'image')
    {
        $requestFile = $request->file($inputName);
        try {
            $dir = 'public/images/' . $name;
            $fixName = time() . '-' . $name . '.' . $requestFile->extension();

            if ($requestFile) {
                Storage::putFileAs($dir, $requestFile, $fixName);
                $request->image = $fixName;
            }

            return $fixName;
        } catch (\Throwable $th) {
            report($th);

            return $th->getMessage();
        }
    }

    public function uploadPhoto($request, $data, $name)
    {
        try {
            $dir = 'public/photos/' . $name;
            $fixName = $data->id . '-' . $name . '.' . $request->file('photo')->extension();
            if ($request->file('photo')) {
                Storage::putFileAs($dir, $request->file('photo'), $fixName);
                $request->photo = $fixName;

                $data->update([
                    'photo' => $request->photo,
                ]);
            }
        } catch (\Throwable $th) {
            report($th);

            return $th->getMessage();
        }
    }

    public function uploadMultiImage($request, $data, $name, $inputName = 'images')
    {
        $requestFiles = $request->file($inputName);

        if (!is_array($requestFiles)) {
            return ['status' => 'Error', 'message' => 'The input must be an array of files for: ' . $inputName];
        }

        $uploadedImages = [];

        foreach ($requestFiles as $file) {
            $dir = 'public_html/images/' . $name;
            $fixName = $data->id . '-' . $name . '_'  . $file->getClientOriginalExtension();

            if ($file) {
                Storage::putFileAs($dir, $file, $fixName);
                $uploadedImages[] = [
                    'url' => $dir . '/' . $fixName,
                ];
            }
        }

        return $uploadedImages;
    }
}
