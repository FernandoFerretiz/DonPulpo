<?php

namespace App\Http\Controllers;

use App\Models\ApkRelease;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApkReleaseController extends Controller
{
    public function index(): View
    {
        $releases = ApkRelease::with('uploader')
            ->orderByDesc('id')
            ->paginate(20);

        return view('apk-releases.index', compact('releases'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'version' => 'required|string|max:50',
            'notes'   => 'nullable|string',
            // Un APK real es un ZIP por dentro, así que el sniffing de
            // contenido de `mimes` lo detecta como "zip", no "apk".
            // Validamos por extensión en su lugar.
            'apk'     => ['required', 'file', 'max:204800', function ($attribute, $value, $fail) {
                if (strtolower($value->getClientOriginalExtension()) !== 'apk') {
                    $fail('El archivo debe ser un .apk.');
                }
            }],
        ]);

        $file = $request->file('apk');
        // store() adivina la extensión por contenido (un APK real sniffea
        // como .zip), así que forzamos el nombre para que quede .apk en disco.
        $filename = \Illuminate\Support\Str::random(40) . '.apk';
        $path = $file->storeAs('apk-releases', $filename, 'public');

        ApkRelease::create([
            'version'       => $validated['version'],
            'notes'         => $validated['notes'] ?? null,
            'original_name' => $file->getClientOriginalName(),
            'file_path'     => $path,
            'size_bytes'    => $file->getSize(),
            'uploaded_by'   => Auth::id(),
        ]);

        return redirect()->route('apk-releases.index')->with('success', 'APK subido correctamente.');
    }

    public function download(ApkRelease $apkRelease): StreamedResponse
    {
        return Storage::disk('public')->download($apkRelease->file_path, $apkRelease->original_name);
    }

    public function destroy(ApkRelease $apkRelease): RedirectResponse
    {
        Storage::disk('public')->delete($apkRelease->file_path);
        $apkRelease->delete();

        return redirect()->route('apk-releases.index')->with('success', 'APK eliminado correctamente.');
    }
}
