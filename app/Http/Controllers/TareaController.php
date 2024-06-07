<?php
namespace App\Http\Controllers;

use App\Models\Tarea;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TareaController extends Controller {
    public function index(Request $request) {
        $query = Tarea::query();
        if ($request->has('descripcion')) {
            $query->where('descripcion', 'like', '%'.$request->input('descripcion') . '%');
        }
    
        if ($request->has('categoria_id')) {
            $query->where('categoria_id', $request->input('categoria_id'));
        }
        
        if ($request->has('completada')) {
            $query->where('completada', $request->input('completada'));
        }
        
        $tareas = $query->paginate(2); // Paginación de 10 tareas por página
        
        // Obtener todas las categorías
        $categorias = Categoria::all();
    
        return view('tareas.index', compact('tareas', 'categorias'));
    }


   public function store(Request $request) {
       $request->validate([
           'descripcion' => 'required',
           'categoria_id' => 'nullable|exists:categorias,id',
       ]);
       Auth::user()->tareas()->create($request->all());
       return redirect()->route('tareas.index');
}

   public function edit(Tarea $tarea) {
       $categorias = Categoria::all();
       return view('tareas.edit', compact('tarea', 'categorias'));
   }

   public function update(Request $request, Tarea $tarea) {
       $request->validate([
           'descripcion' => 'required',
           'categoria_id' => 'nullable|exists:categorias,id',
       ]);

       $tarea->update($request->all());
       return redirect()->route('tareas.index')->with('success', 'Tarea actualizada con éxito.');
   }

   public function toggle(Request $request, Tarea $tarea) {
    $tarea->completed = !$tarea->completed;
    $tarea->save();
    return redirect()->route('tareas.index');
}

}
