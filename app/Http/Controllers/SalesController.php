<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sales = Sale::all();

        return response()->json($sales, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'date' => 'required',
            'payment_type' => 'required',
            'customer_id' => 'required',
            'user_id' => 'required',
            'details' => 'required|array|min:1',
            'details.*.product_id' => 'required',
            'details.*.quantity' => 'required',
            'details.*.price' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['error' => 'Error de validación', 'mensaje' => $validator->errors()], 400);
        }

        try {
            DB::beginTransaction();

            $sale = new Sale();
            $sale->date = $request->date;
            $sale->user_id = $request->user_id;
            $sale->customer_id = $request->customer_id;
            $sale->save();

            $total = 0;
            $quantity = 0;
            $saleDetailsArray = [];

            foreach ($request->input('details') as $detail) {
                $saleDetail = new SaleDetail();
                $saleDetail->product_id = $detail['product_id'];
                $saleDetail->quantity = $detail['quantity'];
                $saleDetail->price = $detail['price'];
                $saleDetail->total = $detail['quantity'] * $detail['price'];
                $saleDetail->sale_id = $sale->id;
                $saleDetail->save();

                $total += $saleDetail->quantity * $saleDetail->price;
                $quantity += $saleDetail->quantity;

                $saleDetailsArray[] = [
                    'product_id' => $saleDetail->product_id,
                    'quantity' => $saleDetail->quantity,
                    'price' => $saleDetail->price,
                    'total' => $saleDetail->total,
                ];
            }
            $sale->total = $total;
            $sale->quantity_items = $quantity;
            $sale->save();

            DB::commit();

            return response()->json(['mensaje' => 'Venta y detalles creados con éxito', 'sale' => $sale, 'order_details' => $saleDetailsArray], 201);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(['error' => 'Error al crear la venta', 'mensaje' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sale  $sales
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $sale = Sale::with('details')->find($id);

        if (!$sale) {
            return response()->json(['mensaje' => 'Venta no encontrada']);
        }

        return response()->json($sale, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sale  $sales
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'date' => 'required',
            'details' => 'required|array|min:1',
            'details.*.product_id' => 'required',
            'details.*.quantity' => 'required',
            'details.*.price' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['error' => 'Error de validación', 'mensaje' => $validator->errors()], 400);
        }
        
        try {
            DB::beginTransaction();

            $sale = Sale::find($id);

            if (!$sale) {
                return response()->json(['mensaje' => 'Venta no encontrada'], 404);
            }

            $sale->update($request->only([
                'date',
                'user_id',
                'customer_id'
            ]));

            $sale->details()->delete();

            $total = 0;
            $quantity = 0;
            $saleDetailsArray = [];
            $payload = [];

            foreach ($request->input('details') as $detail) {
                $saleDetail = $sale->details()->create([
                    'product_id' => $detail['product_id'],
                    'quantity' => $detail['quantity'],
                    'price' => $detail['price'],
                    'total' => $detail['quantity'] * $detail['price']
                ]);

                $total += $saleDetail->quantity * $saleDetail->price;
                $quantity += $saleDetail->quantity;

                $saleDetailsArray[] = [
                    'product_id' => $saleDetail->product_id,
                    'quantity' => $saleDetail->quantity,
                    'price' => $saleDetail->price,
                    'total' => $saleDetail->total,
                ];

                $payload[] = [
                    'product_id' => $saleDetail->product_id,
                    'quantity' => $saleDetail->quantity,
                ];
            }

            /* if ($sale->status === 'delivered') {
                $response = Http::post('http://ruta-del-microservicio-de-inventario/actualizar-stock', [
                    'sale_detail' => $payload,
                ]);

                if (!$response->successful()) {
                    DB::rollback();
                    return response()->json(['error' => 'Error al actualizar el stock en el microservicio de inventario', 'mensaje' => $response->body()], $response->status());
                }
            } */

            $sale->total = $total;
            $sale->quantity_items = $quantity;
            $sale->save();

            DB::commit();

            return response()->json(['mensaje' => 'Venta y detalles actualizados con éxito', 'sale' => $sale, 'sale_details' => $saleDetailsArray], 200);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(['error' => 'Error al actualizar la venta', 'mensaje' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sale  $sales
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $sale = Sale::find($id);

        if (!$sale) {
            return response()->json(['mensaje' => 'Venta no encontrada'], 404);
        }

        try {
            DB::beginTransaction();

            $sale->details()->delete();
            $sale->delete();

            DB::commit();

            return response()->json(['mensaje' => 'Venta y detalles eliminados con éxito'], 200);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(['error' => 'Error al eliminar la venta', 'mensaje' => $e->getMessage()], 500);
        }
    }
}
