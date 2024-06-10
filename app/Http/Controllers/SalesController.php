<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

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

                //Actualizar stock de productos
                $response = Http::put("http://localhost:3000/api/producto/{$saleDetail->product_id}/decrementar-stock", [
                    'quantity' => $saleDetail->quantity
                ]);

                if ($response->failed()) {
                    throw new \Exception("Error al actualizar el stock del producto ID: {$saleDetail->product_id}");
                }
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

            //Actualizar stock de productos
            $response = Http::put("http://localhost:3000/api/producto/{$saleDetail->product_id}/decrementar-stock", [
                'quantity' => $saleDetail->quantity
            ]);

            if ($response->failed()) {
                throw new \Exception("Error al actualizar el stock del producto ID: {$saleDetail->product_id}");
            }

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

    public function getTotalSalesByYear()
    {
        try {
            $sales = Sale::select(
                DB::raw('YEAR(date) as year'),
                DB::raw('SUM(total) as total_sales')
            )
                ->groupBy('year')
                ->get();

            return response()->json(['total_sales_by_year' => $sales], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener el total de ventas por año', 'mensaje' => $e->getMessage()], 500);
        }
    }

    public function getTotalSalesByMonth($year)
    {
        try {
            $sales = Sale::select(
                DB::raw('MONTH(date) as month'),
                DB::raw('SUM(total) as total_sales')
            )
                ->whereYear('date', $year)
                ->groupBy('month')
                ->get();

            return response()->json(['total_sales_by_month' => $sales], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener el total de ventas por mes', 'mensaje' => $e->getMessage()], 500);
        }
    }

    public function getTotalSalesByDateRange(Request $request)
    {
        $rules = [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['error' => 'Error de validación', 'mensaje' => $validator->errors()], 400);
        }

        try {
            $salesByMonth = Sale::select(
                DB::raw('YEAR(date) as year'),
                DB::raw('MONTH(date) as month'),
                DB::raw('SUM(total) as total_sales')
            )
                ->whereBetween('date', [$request->start_date, $request->end_date])
                ->groupBy(DB::raw('YEAR(date)'), DB::raw('MONTH(date)'))
                ->get();

            return response()->json(['total_sales_by_month' => $salesByMonth], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener el total de ventas por rango de fecha', 'mensaje' => $e->getMessage()], 500);
        }
    }

    public function getRecurringCustomers()
    {
        try {
            $recurringCustomers = Sale::select(
                'customer_id',
                DB::raw('COUNT(*) as total_purchases'),
                DB::raw('SUM(total) as total_spent')
            )
                ->groupBy('customer_id')
                ->having('total_purchases', '>', 1) // Filtrar clientes con más de una compra
                ->orderBy('total_purchases', 'desc')
                ->get();

            // Obtener información detallada de cada cliente recurrente
            $customersDetails = [];
            foreach ($recurringCustomers as $customer) {
                $customerDetails = [];
                $customerDetails['customer_id'] = $customer->customer_id;
                $customerDetails['total_purchases'] = $customer->total_purchases;
                $customerDetails['total_spent'] = $customer->total_spent;

                // Puedes agregar más información del cliente aquí si lo deseas, como su nombre, correo electrónico, etc.
                // Ejemplo: $customerDetails['name'] = Customer::find($customer->customer_id)->name;

                $customersDetails[] = $customerDetails;
            }

            return response()->json(['recurring_customers' => $customersDetails], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener los clientes recurrentes', 'mensaje' => $e->getMessage()], 500);
        }
    }


}
