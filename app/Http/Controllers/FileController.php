<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use Illuminate\Support\Str;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->is_super_admin) {
            $files = File::latest()->paginate(10);
        } else {
            $files = File::where('user_id_created', $user->id)
                        ->latest()
                        ->paginate(10);
        }

        return view('files.index', compact('files'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('files.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'shop_name' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        try {
            $fileUpload = $request->file('file');

            $filename = time() . '_' . Str::slug($request->shop_name) . '.' . $fileUpload->getClientOriginalExtension();

            $disk = 'public';
            $folder = $fileUpload->extension() === 'pdf' ? 'pdfs' : 'images';
            $filePath = $fileUpload->storeAs($folder, $filename, $disk);

            $fileUrl = url('storage/' . $filePath);

            $qrName = 'qr_' . time() . '_' . Str::random(6) . '.png';
            $qrPath = 'qrs/' . $qrName;

            $qrCode = Builder::create()
                ->writer(new PngWriter())
                ->data($fileUrl)
                ->size(300)
                ->margin(10)
                ->build();

            Storage::disk($disk)->put($qrPath, $qrCode->getString());

            $file = File::create([
                'shop_name' => $request->shop_name,
                'file_path' => $filePath,
                'qr_path' => $qrPath,
                'user_id_created' => auth()->id(),
            ]);

        } catch (\Exception $e) {
            \Log::error('File upload failed', ['exception' => $e]);
            return back()->withInput()->withErrors(['file' => 'Upload or QR generation failed.']);
        }

        return redirect()->route('files.show', $file->id)->with('success', 'Upload success');
    }


    /**
     * Display the specified resource.
     */
    public function show(File $file)
    {
        $fileUrl = $file->file_path ? asset('storage/' . $file->file_path) : null;
        $qrUrl = $file->qr_path ? asset('storage/' . $file->qr_path) : null;

        return view('files.show', compact('file', 'fileUrl', 'qrUrl'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(File $file)
    {
        try {
            $disk = 'public';
            if ($file->file_path && Storage::disk($disk)->exists($file->file_path)) {
                Storage::disk($disk)->delete($file->file_path);
            }

            if ($file->qr_path && Storage::disk($disk)->exists($file->qr_path)) {
                Storage::disk($disk)->delete($file->qr_path);
            }

            $file->delete();

        } catch (\Exception $e) {
            \Log::error('Failed to delete file:', ['exception' => $e]);
            return back()->withErrors(['error' => 'Failed to delete file.']);
        }

        return redirect()->route('files.index')->with('success', 'File deleted successfully');
    }
}
