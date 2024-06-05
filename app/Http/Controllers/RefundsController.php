<?php

namespace App\Http\Controllers;

use App\Models\Refund;
use Illuminate\Http\Request;

class RefundsController extends Controller
{
    public function index()
    {
        $Refund = Refund::all();
        return response()->json($Refund);
    }

    // Store a newly created refund
    public function store(Request $request)
    {
        $validatedData = new Refund();

            $validatedData->date  = $request->date;
            $validatedData->reason = $request->reason;
            $validatedData->quantity = $request->quantity;
            $validatedData->customer_id = $request->customer_id;
            $validatedData->detail_sale_id =$request->detail_sale_id;

        $validatedData->save();

        return response()->json($validatedData, 201);
    }

    // Display the specified refund
    public function show($id)
    {
        $refund = Refund::with('detailSale')->find($id);

        if (!$refund) {
            return response()->json(['message' => 'Refund not found'], 404);
        }

        return response()->json($refund);
    }

    public function update(Request $request, $id)
    {
        // Validar los datos de entrada
        $validatedData = new Refund;

        $validatedData->date  = $request->date;
        $validatedData->reason = $request->reason;
        $validatedData->quantity = $request->quantity;
        $validatedData->customer_id = $request->customer_id;

        // Buscar la venta existente
        $refund = Refund::find($id);
        // Verificar si la venta existe
        if (!$refund) {
            return response()->json(['message' => 'Sale not found'], 404);
        }

        // Actualizar los datos de la venta
        $refund->date = $validatedData['date'];
        $refund->reason = $validatedData['reason'];
        $refund->quantity = $validatedData['quantity'];
        $refund->customer_id = $validatedData['customer_id'];

        $refund->save();

        // Devolver la respuesta JSON
        return response()->json($refund, 200);
    }

    // Remove the specified refund
    public function destroy($id)
    {
        $refund = Refund::find($id);

        if (!$refund) {
            return response()->json(['message' => 'Refund not found'], 404);
        }

        $refund->delete();
        return response()->json(['message' => 'Refund deleted']);
    }
}
