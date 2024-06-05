<?php

namespace App\Http\Controllers;

use App\Models\SaleDetail;
use Illuminate\Http\Request;

class DetailSaleController extends Controller
{
     // List all detail sales
    public function index()
    {
        $detailSales = SaleDetail::all();
        return response()->json($detailSales);
    }

    // Store a newly created detail sale
    public function store(Request $request)
    {
        $validatedData = new SaleDetail();

        
        $validatedData->quantity = $request->quantity;
        $validatedData->price = $request->price;
        $validatedData->total = $request->total;
        $validatedData->sale_id = $request->sale_id;
        $validatedData->product_id = $request->product_id;

        $validatedData->save();

        return response()->json($validatedData, 201);
    }

    // Display the specified detail sale
    public function show($id)
    {
        
        $detailSale = SaleDetail::find($id);

        if (!$detailSale) {
            return response()->json(['message' => 'Detail Sale not found'], 404);
        }

        return response()->json($detailSale);
    }

    // Update the specified detail sale
    public function update(Request $request, $id)
    {
        // Find the existing detail sale
        $detailSale = SaleDetail::find($id);

        if (!$detailSale) {
            return response()->json(['message' => 'Detail Sale not found'], 404);
        }

        // Update the detail sale
        $detailSale->quantity = $request->quantity;
        $detailSale->price = $request->price;
        $detailSale->total = $request->total;
        $detailSale->sale_id = $request->sale_id;
        $detailSale->refund_id = $request->refund_id;
        $detailSale->product_id = $request->product_id;

        $detailSale->save();

        return response()->json($detailSale, 200);
    }

    // Remove the specified detail sale
    public function destroy($id)
    {
        $detailSale = SaleDetail::find($id);

        if (!$detailSale) {
            return response()->json(['message' => 'Detail Sale not found'], 404);
        }

        $detailSale->delete();
        return response()->json(['message' => 'Detail Sale deleted']);
    }
}
