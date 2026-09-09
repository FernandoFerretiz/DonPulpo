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
        // El APK puede tardar en subir en conexiones lentas: se amplía el
        // límite de tiempo y memoria solo para esta acción.
        ini_set('max_execution_time', '300');
        ini_set('memory_limit', '256M');
        set_time_limit(300);

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

    /**
     * Alternativa a subir el archivo: registrar una versión que solo apunta a un
     * link externo (Drive, S3, etc.), para cuando la subida directa falla.
     */
    public function storeLink(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'version'      => 'required|string|max:50',
            'notes'        => 'nullable|string',
            'external_url' => 'required|url|max:2048',
        ]);

        ApkRelease::create([
            'version'       => $validated['version'],
            'notes'         => $validated['notes'] ?? null,
            'original_name' => 'app-cobro-release.apk',
            'external_url'  => $validated['external_url'],
            'uploaded_by'   => Auth::id(),
        ]);

        return redirect()->route('apk-releases.index')->with('success', 'Versión registrada con link externo.');
    }

    public function download(ApkRelease $apkRelease): StreamedResponse|RedirectResponse
    {
        if ($apkRelease->isExternal()) {
            return redirect()->away($apkRelease->external_url);
        }

        return Storage::disk('public')->download($apkRelease->file_path, $apkRelease->original_name);
    }

    public function destroy(ApkRelease $apkRelease): RedirectResponse
    {
        if (! $apkRelease->isExternal()) {
            Storage::disk('public')->delete($apkRelease->file_path);
        }
        $apkRelease->delete();

        return redirect()->route('apk-releases.index')->with('success', 'APK eliminado correctamente.');
    }
}
