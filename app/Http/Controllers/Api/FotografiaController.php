<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fotografia;
use App\Models\User;
use App\Http\Resources\FotografiaResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Importar Intervention Image V3
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\AutoEncoder;

class FotografiaController extends Controller
{
    // Listar todas las fotos (solo públicas)
    public function index()
    {
        $fotografias = Fotografia::with('user', 'likes', 'comentarios')
            ->where('vetada', false)
            ->orderBy('id', 'desc')
            ->paginate(10); // paginación opcional

        return FotografiaResource::collection($fotografias);
    }

    // Listar TODAS las fotos para admin (incluidas vetadas)
    public function adminIndex(Request $request)
    {
        if ($request->user()->rol !== 'admin') {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $fotografias = Fotografia::with('user', 'likes', 'comentarios')
            ->orderBy('id', 'desc')
            ->get(); // Sin paginacion para el panel de admin por ahora
        
        return FotografiaResource::collection($fotografias);
    }

    // Actualizar foto (Admin edit/veto)
    public function update(Request $request, $id)
    {
        $foto = Fotografia::find($id);
        if (!$foto) return response()->json(['error' => 'Foto no encontrada'], 404);

        $user = $request->user();
        
        // Solo admin o el dueño pueden editar
        if ($user->id !== $foto->usuario_id && $user->rol !== 'admin') {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $request->validate([
            'titulo' => 'sometimes|string|max:255',
            'descripcion' => 'sometimes|string',
            'vetada' => 'sometimes|boolean',
        ]);

        // Si es admin puede tocar 'vetada', si no es admin, ignoramos ese campo o damos error.
        // Aquí simplificamos: actualizamos todo lo que venga.
        // Pero idealmente solo admin debería poder enviar 'vetada=true'.
        
        $data = $request->only(['titulo', 'descripcion']);

        if ($user->rol === 'admin' && $request->has('vetada')) {
            $data['vetada'] = $request->vetada;
        }

        $foto->update($data);

        return new FotografiaResource($foto);
    }

    // Mostrar las fotos de un usuario logueado
    public function misFotos(Request $request)
    {
        $user = $request->user(); // auth:sanctum
        $misFotos = $user ? $user->fotografias()->with('likes', 'comentarios')->get() : collect();

        return FotografiaResource::collection($misFotos);
    }

    public function fotografiasUsuario(Request $request, $id)
    {
        $user = User::find($id); // Este sera el ID del usuario cuyas fotos queremos ver
        // Se añade 'user' a las relaciones cargadas para que el frontend pueda leer userName
        $fotosusuario = $user ? $user->fotografias()->with('user', 'likes', 'comentarios')->get() : collect();

        return FotografiaResource::collection($fotosusuario);
    }

    // Mostrar una foto concreta
    public function show($id)
    {
        $foto = Fotografia::with('user', 'likes', 'comentarios')->find($id);

        if (!$foto) {
            return response()->json(['error' => 'Foto no encontrada'], 404);
        }

        return new FotografiaResource($foto);
    }

    // Eliminar foto (solo dueño o admin)
    public function destroy(Request $request, $id)
    {
        $foto = Fotografia::find($id);

        if (!$foto) {
            return response()->json(['error' => 'Foto no encontrada'], 404);
        }

        $user = $request->user();
        // Allow if user is owner OR user is admin
        if ($user->id !== $foto->usuario_id && $user->rol !== 'admin') {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $foto->delete();

        return response()->json(['message' => 'Foto eliminada correctamente']);
    }

    public function store(Request $request)
    {
        // 1. Validación
        // Eliminamos 'usuario_id' de las reglas porque lo tomamos del token de autenticación
        $request->validate([
            'direccion_imagen'     => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:10240', // Max 10MB por ejemplo
            'titulo'               => 'required|max:255',
            'descripcion'          => 'required',
            // Metadatos
            'ISO'                  => 'required|integer|min:50|max:51200',
            'velocidad_obturacion' => 'required|string|max:20',
            'apertura'             => 'required|numeric|min:0.7|max:32',
            'latitud'              => 'nullable|numeric',
            'longitud'             => 'nullable|numeric',
        ]);

        try {
            // 2. Gestión del archivo
            $file = $request->file('direccion_imagen');
            $extension = $file->getClientOriginalExtension();
            $nombreArchivo = time() . '.' . $extension;

            // Rutas físicas
            $pathOriginal = public_path('images/original');
            $pathOptimizado = public_path('images/optimizadas');

            // Asegurarnos de que las carpetas existen
            if (!file_exists($pathOriginal)) mkdir($pathOriginal, 0777, true);
            if (!file_exists($pathOptimizado)) mkdir($pathOptimizado, 0777, true);

            // Mover la imagen original
            $file->move($pathOriginal, $nombreArchivo);
            $rutaCompletaOriginal = $pathOriginal . '/' . $nombreArchivo;
            $rutaCompletaOptimizada = $pathOptimizado . '/' . $nombreArchivo;

            // 3. Optimización con Intervention Image V3
            $manager = new ImageManager(new Driver());
            
            // Leemos la imagen original
            $image = $manager->read($rutaCompletaOriginal);

            // Redimensionamos (800px ancho, alto automático)
            $image->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize(); // Evita que imágenes pequeñas se pixelen al agrandarse
            });

            // Codificamos y guardamos la optimizada
            $encoder = new AutoEncoder(quality: 65);
            $encodedImage = $image->encode($encoder);
            $encodedImage->save($rutaCompletaOptimizada);

            // 4. Crear registro en Base de Datos
            $fotografia = new Fotografia;
            // IMPORTANTE: Usamos el ID del usuario autenticado (Token), no el del request
            $fotografia->usuario_id = $request->user()->id; 
            
            $fotografia->direccion_imagen = 'original/' . $nombreArchivo;
            $fotografia->direccion_optimizada = 'optimizadas/' . $nombreArchivo;
            $fotografia->titulo = $request->titulo;
            $fotografia->descripcion = $request->descripcion;
            
            // Metadatos
            $fotografia->ISO = $request->ISO;
            $fotografia->velocidad_obturacion = $request->velocidad_obturacion;
            $fotografia->apertura = $request->apertura;
            $fotografia->latitud = $request->latitud;
            $fotografia->longitud = $request->longitud;
            
            // Por defecto vetada false (si aplica a tu lógica)
            $fotografia->vetada = false; 

            $fotografia->save();

            // 5. Retornar respuesta JSON
            // Devolvemos el recurso creado con código 201 (Created)
            return response()->json([
                'message' => 'Fotografía subida con éxito',
                'data' => new FotografiaResource($fotografia)
            ], 201);

        } catch (\Exception $e) {
            // Manejo de errores básico por si falla la librería de imagen
            return response()->json([
                'error' => 'Error al procesar la imagen',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    // Búsqueda avanzada de fotografías
    public function buscar(Request $request)
    {
        $query = Fotografia::with('user', 'likes', 'comentarios')->where('vetada', false);

        // Búsqueda por texto (título o descripción)
        if ($request->filled('texto')) {
            $texto = $request->input('texto');
            $query->where(function ($q) use ($texto) {
                $q->where('titulo', 'like', "%{$texto}%")
                  ->orWhere('descripcion', 'like', "%{$texto}%");
            });
        }

        // Búsqueda por usuario
        if ($request->filled('usuario')) {
            $usuario = $request->input('usuario');
            $query->whereHas('user', function ($q) use ($usuario) {
                $q->where('name', 'like', "%{$usuario}%");
            });
        }

        // Búsqueda por ISO
        if ($request->filled('iso')) {
            $query->where('ISO', $request->input('iso'));
        }

        // Búsqueda por fecha (si se pasó una fecha específica, busca registros de ese día)
        // Se usa created_at o id si timestamps está deshabilitado. Probemos con id que es aprox a fecha temporal.
        if ($request->filled('fecha')) {
            $fecha = $request->input('fecha');
            // Como timestamps está en false pero la migración los creó, intentamos buscar por fecha exacta si hay algo,
            // pero si no, intentamos asegurar. Lo más simple si los timestamps no funcionan es no fallar y usar created_at o ID aproximado.
            // Asumiendo que created_at si se rellena por base de datos:
            $query->whereDate('created_at', $fecha);
        }

        return FotografiaResource::collection($query->orderBy('id', 'desc')->get());
    }
}
