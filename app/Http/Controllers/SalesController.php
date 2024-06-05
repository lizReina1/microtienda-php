<?php

namespace App\Http\Controllers;

use App\Models\sales;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sales= sales::all();

        return response()->json($sales);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = new sales;

            $validatedData->date  = $request->date;
            $validatedData->total = $request->total;
            $validatedData->payment_type = $request->payment_type;
            $validatedData->quantity_items = $request->quantity_items;
            $validatedData->customer_id = $request->customer_id;
            $validatedData->user_id = $request->user_id;

        $validatedData->save();

        return response()->json($validatedData, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sales  $sales
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $sales = sales::findOrFail($id);
        return response()->json($sales);
    }

    /**
     * Search for a resource by ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function findById($id)
    {
        $sales = sales::find($id);

        if (!$sales) {
            return response()->json(['message' => 'Sale not found'], 404);
        }

        return response()->json($sales);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Sales  $sales
     * @return \Illuminate\Http\Response
     */
    public function edit(Sales $sales)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sales  $sales
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Validar los datos de entrada
        $validatedData = new sales;

        $validatedData->date  = $request->date;
        $validatedData->total = $request->total;
        $validatedData->payment_type = $request->payment_type;
        $validatedData->quantity_items = $request->quantity_items;
        $validatedData->customer_id = $request->customer_id;
        $validatedData->user_id = $request->user_id;

        // Buscar la venta existente
        $sale = sales::find($id);
        // Verificar si la venta existe
        if (!$sale) {
            return response()->json(['message' => 'Sale not found'], 404);
        }

        // Actualizar los datos de la venta
        $sale->date = $validatedData['date'];
        $sale->total = $validatedData['total'];
        $sale->payment_type = $validatedData['payment_type'];
        $sale->quantity_items = $validatedData['quantity_items'];
        $sale->customer_id = $validatedData['customer_id'];
        $sale->user_id = $validatedData['user_id'];

        // Guardar los cambios
        $sale->save();

        // Devolver la respuesta JSON
        return response()->json($sale, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sales  $sales
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Sales $sales)
    {
        $sales->delete();
        return response()->json(null, 204);
    }
}
