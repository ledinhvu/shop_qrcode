<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use Illuminate\Support\Str;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->is_super_admin) {
            $shops = Shop::latest()->paginate(10);
        } else {
            $shops = Shop::where('user_id_created', $user->id)
                        ->latest()
                        ->paginate(10);
        }

        return view('shops.index', compact('shops'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('shops.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'shop_name' => 'required|string|max:255',
            'pdf' => 'required|file|mimes:pdf|max:10240',
        ]);

        try {
            $pdfFile = $request->file('pdf');
            $filename = time() . '_' . Str::slug($request->shop_name) . '.' . $pdfFile->getClientOriginalExtension();
            $pdfPath = $pdfFile->storeAs('pdfs', $filename, 'public');

            $fileUrl = url('storage/' . $pdfPath);

            $qrName = 'qr_' . time() . '_' . Str::random(6) . '.png';
            $qrPath = 'qrs/' . $qrName;

            $qrCode = Builder::create()
                ->writer(new PngWriter())
                ->data($fileUrl)
                ->size(300)
                ->margin(10)
                ->build();

            Storage::disk('public')->put($qrPath, $qrCode->getString());

            $shop = Shop::create([
                'shop_name' => $request->shop_name,
                'pdf_path' => $pdfPath,
                'qr_path' => $qrPath,
                'user_id_created' => auth()->id(),
            ]);

        } catch (\Exception $e) {
            \Log::error('Shop upload failed', ['exception' => $e]);
            return back()->withInput()->withErrors(['pdf' => 'Upload or QR generation failed.']);
        }

        return redirect()->route('shops.show', $shop->id)->with('success', 'Upload success');
    }

    /**
     * Display the specified resource.
     */
    public function show(Shop $shop)
    {
        $pdfUrl = $shop->pdf_path ? asset('storage/' . $shop->pdf_path) : null;
        $qrUrl = $shop->qr_path ? asset('storage/' . $shop->qr_path) : null;

        return view('shops.show', compact('shop', 'pdfUrl', 'qrUrl'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shop $shop)
    {
        try {
            if ($shop->pdf_path && Storage::disk('public')->exists($shop->pdf_path)) {
                Storage::disk('public')->delete($shop->pdf_path);
            }

            if ($shop->qr_path && Storage::disk('public')->exists($shop->qr_path)) {
                Storage::disk('public')->delete($shop->qr_path);
            }

            $shop->delete();

        } catch (\Exception $e) {
            \Log::error('Failed to delete shop:', ['exception' => $e]);
            return back()->withErrors(['error' => 'Failed to delete shop.']);
        }

        return redirect()->route('shops.index')->with('success', 'Shop deleted successfully');
    }
}
