<?php

namespace Src\ConsultaCliente\Application\Controllers;

use App\Http\Controllers\Controller;
use Src\ConsultaCliente\Infrastructure\Requests\ConsultarOrdenRequest;
use Src\ConsultaCliente\Infrastructure\Requests\AprobarOrdenRequest;
use Src\ConsultaCliente\Infrastructure\Requests\RechazarOrdenRequest;
use Src\ConsultaCliente\Infrastructure\Resources\ConsultaOrdenResource;
use Src\OrdenReparacion\Infrastructure\Models\OrdenReparacionEloquentModel;
use Src\OrdenReparacion\Application\Services\OrdenReparacionService;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class ConsultaClienteController extends Controller
{
    public function __construct(private OrdenReparacionService $service) {}

    public function index(): Response
    {
        return Inertia::render('ConsultaCliente/consultar');
    }

    public function buscar(ConsultarOrdenRequest $request)
    {
        $validated = $request->validated();

        $orden = OrdenReparacionEloquentModel::where('numero_orden', $validated['numeroOrden'])
            ->whereHas('cliente', function($q) use ($validated) {
                $q->where('numero_documento', $validated['numeroDocumento']);
            })
            ->with(['cliente', 'equipo', 'tecnico', 'repuestos'])
            ->first();

        if (!$orden) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'No se encontró la orden de reparación. Verifique los datos ingresados.');
        }

        return Inertia::render('ConsultaCliente/detalle', [
            'orden' => new ConsultaOrdenResource($orden)
        ]);
    }

    public function aprobar(AprobarOrdenRequest $request, string $id): RedirectResponse
    {
        $orden = OrdenReparacionEloquentModel::find($id);

        if (!$orden || $orden->estado !== 'pendiente_aprobacion') {
            return redirect()->back()
                ->with('error', 'La orden no está disponible para aprobación.');
        }

        if ($request->input('numeroDocumento') !== $orden->cliente->numero_documento) {
            return redirect()->back()
                ->with('error', 'Datos de verificación incorrectos.');
        }

        $this->service->aprobarOrden($orden);

        return redirect()->route('consulta.buscar', [
            'numeroOrden' => $orden->numero_orden,
            'numeroDocumento' => $request->input('numeroDocumento')
        ])->with('success', 'Reparación aprobada exitosamente. Procederemos con el servicio.');
    }

    public function rechazar(RechazarOrdenRequest $request, string $id): RedirectResponse
    {
        $orden = OrdenReparacionEloquentModel::find($id);

        if (!$orden || $orden->estado !== 'pendiente_aprobacion') {
            return redirect()->back()
                ->with('error', 'La orden no está disponible para rechazo.');
        }

        if ($request->input('numeroDocumento') !== $orden->cliente->numero_documento) {
            return redirect()->back()
                ->with('error', 'Datos de verificación incorrectos.');
        }

        $this->service->rechazarOrden($orden, $request->input('motivoRechazo'));

        return redirect()->route('consulta.buscar', [
            'numeroOrden' => $orden->numero_orden,
            'numeroDocumento' => $request->input('numeroDocumento')
        ])->with('success', 'Reparación rechazada. Su equipo estará listo para retiro.');
    }
}
