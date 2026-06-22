<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reporte;
use App\Models\Fotografia;
use Illuminate\Support\Facades\DB; // Nos permite insertar consultar directamente a la base de datos
use Illuminate\Support\Facades\Auth;

class ReporteController extends Controller
{

    //**************************************************************/
    //**************************************************************/
    //                Mandamos los reportes a la vista
    //**************************************************************/
    //**************************************************************/

    public function index()
    {
        // 1) Si está autenticado y vetado, lo mando a /vetado
        if (Auth::check() && Auth::user()->estaVetado()) {
            return redirect()->route('vetado');
        }

        // 2) Si no está vetado, muestro normalmente
        $reportes = Reporte::select('foto_id', DB::raw('count(*) as total_reportes'))
            ->with('foto')
            ->groupBy('foto_id')
            ->get();

        return inertia('Admin/Reportes', [
            'reportes' => $reportes
        ]);
    }

    //**************************************************************/
    //**************************************************************/
    //           Nos muestra los detalles de un reporte
    //**************************************************************/
    //**************************************************************/

    public function detalle($foto_id)
    {
        $foto = Fotografia::with(['user', 'likes', 'comentarios'])->findOrFail($foto_id);

        $reportes = Reporte::where('foto_id', $foto_id)
            ->with('usuario')
            ->get();

        return inertia('Admin/ReporteDetalle', [
            'foto' => $foto,
            'reportes' => $reportes
        ]);
    }

    //**************************************************************/
    //**************************************************************/
    //                Visualizamos las fotografias
    //**************************************************************/
    //**************************************************************/

    public function create($id)
    {
        $fotografia = Fotografia::findOrFail($id);
        return inertia('Fotografias/Reportar', [
            'fotografia' => $fotografia
        ]);
    }

    //**************************************************************/
    //**************************************************************/
    //                   guardamos un reporte
    //**************************************************************/
    //**************************************************************/

    public function store(Request $request)
    {
        $request->validate([
            'fotografia_id' => 'required|exists:fotografias,id',
            'motivo' => 'required|string|max:1000',
        ]);

        $existe = Reporte::where('foto_id', $request->fotografia_id)
            ->where('usuario_id', Auth::id())
            ->exists();

        if ($existe) {
            return redirect()->back()->with('error', 'Ya has reportado esta fotografía previamente.');
        }

        Reporte::create([
            'foto_id' => $request->fotografia_id,
            'usuario_id' => Auth::id(),
            'motivo' => $request->motivo,
        ]);

        return redirect()->route('fotografias.index')->with('success', '¡Reporte enviado con éxito!');
    }

    //**************************************************************/
    //**************************************************************/
    //              Eliminamos los reportes de una foto
    //**************************************************************/
    //**************************************************************/

    public function eliminarPorFoto($foto_id)
    {
        Reporte::where('foto_id', $foto_id)->delete();

        return redirect()->route('admin.reportes')->with('success', 'Todos los reportes de la foto fueron eliminados.');
    }

}
