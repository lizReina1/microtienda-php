<?php

namespace App\Http\Controllers;

use App\Models\Refund;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function import(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        $filePath = $file->getRealPath();

        $file = fopen($filePath, 'r');
        $header = fgetcsv($file); // Asumiendo que la primera fila es la cabecera

        while (($row = fgetcsv($file, 1000, ',')) !== false) {
            $data = array_combine($header, $row);
            $date = \DateTime::createFromFormat('m/d/Y', $data['date'])->format('Y-m-d');

            // Ajusta esto según los nombres de tus columnas y el modelo
            $validatedData = new Sale;
            $validatedData->date  = $date;
            $validatedData->total = $data['total'];
            $validatedData->payment_type = $data['payment_type'];
            $validatedData->quantity_items = $data['quantity_items'];
            $validatedData->customer_id = $data['customer_id'];
            $validatedData->user_id = $data['user_id'];

            $validatedData->save();

        }

        fclose($file);

        return response()->json(['message' => 'File imported successfully'], 200);
    }

    public function importRefunds(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        $filePath = $file->getRealPath();

        $file = fopen($filePath, 'r');
        $header = fgetcsv($file); // Asumiendo que la primera fila es la cabecera

        while (($row = fgetcsv($file, 1000, ',')) !== false) {
            $data = array_combine($header, $row);
            $date = \DateTime::createFromFormat('m/d/Y', $data['date'])->format('Y-m-d');

            $validatedData = new Refund();

            $validatedData->date  = $date;
            $validatedData->reason = $data['reason'];
            $validatedData->quantity = $data['quantity'];
            $validatedData->customer_id = $data['customer_id'];
            $validatedData->detail_sale_id = $data['sale_detail_id'];
            $validatedData->save();

        }

        fclose($file);

        return response()->json(['message' => 'File imported successfully'], 200);
    }

    public function importDetailSale(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        $filePath = $file->getRealPath();

        $file = fopen($filePath, 'r');
        $header = fgetcsv($file); // Asumiendo que la primera fila es la cabecera

        while (($row = fgetcsv($file, 1000, ',')) !== false) {
            $data = array_combine($header, $row);

            $validatedData = new SaleDetail();

            $validatedData->quantity = $data['quantity'];
            $validatedData->price = $data['price'];
            $validatedData->total = $data['total'];
            $validatedData->sale_id = $data['sale_id'];
            $validatedData->product_id = $data['product_id'];

            $validatedData->save();

        }

        fclose($file);

        return response()->json(['message' => 'File imported successfully'], 200);
    }
}
