<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Services\ImageVariants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Administración de los banners y el video de la portada de la tienda.
 */
class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::deTipo(Banner::TIPO_BANNER)->orderBy('orden')->orderBy('id')->get();
        $videos = Banner::deTipo(Banner::TIPO_VIDEO)->orderBy('orden')->orderBy('id')->get();

        return view('banners.index', compact('banners', 'videos'));
    }

    public function create(Request $request)
    {
        $banner = new Banner([
            'tipo' => $request->query('tipo') === Banner::TIPO_VIDEO ? Banner::TIPO_VIDEO : Banner::TIPO_BANNER,
            'activo' => true,
            'orden' => (int) Banner::max('orden') + 1,
        ]);

        return view('banners.create', compact('banner'));
    }

    public function store(Request $request)
    {
        $data = $this->validar($request, null);

        foreach (['imagen', 'imagen_movil', 'video'] as $campo) {
            if ($request->hasFile($campo)) {
                $data[$campo] = $this->guardar($request, $campo);
            }
        }

        Banner::create($data);

        return redirect()->route('banners.index')->with('success', 'Guardado en la portada.');
    }

    public function edit(Banner $banner)
    {
        return view('banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $data = $this->validar($request, $banner);

        foreach (['imagen', 'imagen_movil', 'video'] as $campo) {
            // Se pueden quitar la imagen móvil y el póster del video; la imagen del banner y el video son obligatorios
            $puedeQuitar = $campo === 'imagen_movil' || ($campo === 'imagen' && $banner->es_video);

            if ($request->hasFile($campo)) {
                $this->borrarArchivo($banner->{$campo}, $campo);
                $data[$campo] = $this->guardar($request, $campo);
            } elseif ($puedeQuitar && $request->boolean("quitar_{$campo}")) {
                $this->borrarArchivo($banner->{$campo}, $campo);
                $data[$campo] = null;
            }
        }

        $banner->update($data);

        return redirect()->route('banners.index')->with('success', 'Cambios guardados.');
    }

    public function toggle(Banner $banner)
    {
        $banner->update(['activo' => !$banner->activo]);

        return redirect()->route('banners.index')
            ->with('success', $banner->activo ? 'Activado: ya se ve en la portada.' : 'Apagado: ya no se muestra.');
    }

    public function destroy(Banner $banner)
    {
        foreach (['imagen', 'imagen_movil', 'video'] as $campo) {
            $this->borrarArchivo($banner->{$campo}, $campo);
        }
        $banner->delete();

        return redirect()->route('banners.index')->with('success', 'Eliminado.');
    }

    private function validar(Request $request, ?Banner $banner): array
    {
        $esVideo = ($banner?->tipo ?? $request->input('tipo')) === Banner::TIPO_VIDEO;

        $reglas = [
            'titulo' => 'required|string|max:120',
            'subtitulo' => 'nullable|string|max:200',
            'texto_boton' => 'nullable|string|max:30',
            'enlace' => ['nullable', 'string', 'max:255', function ($attr, $value, $fail) {
                if ($value && !str_starts_with($value, '/') && !filter_var($value, FILTER_VALIDATE_URL)) {
                    $fail('El enlace debe ser una dirección completa (https://…) o una ruta interna que empiece con "/", por ejemplo /tienda.');
                }
            }],
            'orden' => 'nullable|integer|min:0|max:9999',
            'inicia_en' => 'nullable|date',
            'termina_en' => 'nullable|date|after_or_equal:inicia_en',
            'tipo' => 'required|in:banner,video',
        ];

        if ($esVideo) {
            $reglas['video'] = [($banner?->video ? 'nullable' : 'required'), 'file', 'mimetypes:video/mp4,video/webm', 'max:20480'];
            $reglas['imagen'] = 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096';
        } else {
            $reglas['imagen'] = [($banner?->imagen ? 'nullable' : 'required'), 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'];
            $reglas['imagen_movil'] = 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096';
        }

        $mensajes = [
            'imagen.required' => 'Sube la imagen del banner.',
            'video.required' => 'Sube el archivo de video.',
            'video.mimetypes' => 'El video debe ser MP4 o WebM.',
            'video.max' => 'El video pesa más de 20 MB. Comprímelo antes de subirlo.',
            'imagen.max' => 'La imagen pesa más de 4 MB. Redúcela antes de subirla.',
            'imagen_movil.max' => 'La imagen pesa más de 4 MB. Redúcela antes de subirla.',
        ];

        $data = $request->validate($reglas, $mensajes);

        $data['activo'] = $request->boolean('activo');
        $data['orden'] = (int) ($data['orden'] ?? 0);
        // Los archivos se guardan aparte (aquí solo van los campos de texto)
        unset($data['imagen'], $data['imagen_movil'], $data['video']);

        // El tipo no cambia al editar
        if ($banner) {
            $data['tipo'] = $banner->tipo;
        }

        return $data;
    }

    /** Guarda el archivo subido y genera sus versiones WebP si es imagen. */
    private function guardar(Request $request, string $campo): string
    {
        if ($campo === 'video') {
            return $request->file('video')->store('banners/videos', 'public');
        }

        $ruta = $request->file($campo)->store('banners', 'public');
        app(ImageVariants::class)->generate(
            $ruta,
            false,
            $campo === 'imagen_movil' ? ImageVariants::BANNER_MOBILE_WIDTHS : ImageVariants::BANNER_WIDTHS
        );

        return $ruta;
    }

    private function borrarArchivo(?string $ruta, string $campo): void
    {
        if (!$ruta) {
            return;
        }

        Storage::disk('public')->delete($ruta);

        if ($campo !== 'video') {
            app(ImageVariants::class)->delete(
                $ruta,
                $campo === 'imagen_movil' ? ImageVariants::BANNER_MOBILE_WIDTHS : ImageVariants::BANNER_WIDTHS
            );
        }
    }
}
