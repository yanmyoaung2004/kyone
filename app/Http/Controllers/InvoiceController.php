<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class InvoiceController extends Controller
{
  
    // 1. CREATE Invoice
    public function store(Request $request)
    {
        try{
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date',
            'total_amount' => 'required|string',
            'status' => 'required|in:unpaid,paid,cancelled',
        ]);

       // "INV65C61E123ABC"
        $invoiceNumber = 'INV' . strtoupper(uniqid());

        Invoice::create([
            'order_id' => $request->order_id,
            'invoice_number' => $request->$invoiceNumber,
            'issue_date' => Carbon::parse($request->issue_date),
            'due_date' => Carbon::parse($request->due_date),
            'total_amount' => $request->total_amount,
            'status' => $request->status,
        ]);

        return response()->json(['message' => 'Invoice created successfully']);
    } catch (ValidationException $e) {
        return response()->json([
            'message' =>$e->errors() 
             
        ], 422);
    }
    }

    // 2. READ all invoices
    public function index()
    {
        return response()->json(Invoice::all());
    }

    // 3. READ a single invoice
    public function show($id)
    {
        $invoice = Invoice::find($id);
        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }
        return response()->json($invoice);
    }

    // 4. UPDATE an invoice
    public function update(Request $request, $id)
    {
        
        $invoice = Invoice::find($id);
        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }
        try{

        $request->validate([
            'order_id' => 'exists:orders,id',
            'invoice_number' => 'unique:invoices,invoice_number,' . $id,
            'issue_date' => 'date',
            'due_date' => 'date',
            'total_amount' => 'string',
            'status' => 'in:unpaid,paid,cancelled',
        ]);

        $invoice->update([
            'order_id' => $request->order_id ?? $invoice->order_id,
            'invoice_number' => $request->invoice_number ?? $invoice->invoice_number,
            'issue_date' => $request->issue_date ? Carbon::parse($request->issue_date) : $invoice->issue_date,
            'due_date' => $request->due_date ? Carbon::parse($request->due_date) : $invoice->due_date,
            'total_amount' => $request->total_amount ?? $invoice->total_amount,
            'status' => $request->status ?? $invoice->status,
        ]);

        return response()->json(['message' => 'Invoice updated successfully']);

    } catch (ValidationException $e) {
        return response()->json([
            'message'=> $e->errors(),
            
        ], 422);
    }
    }

    // 5. DELETE an invoice
    public function destroy($id)
    {
        $invoice = Invoice::find($id);
        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }
        $invoice->delete();

        return response()->json(['message' => 'Invoice deleted successfully']);
    }

    public function customerInvoices($customerId, Request $request){
        $perPage = $request->input('per_page', 10); 
        $page = $request->input('page', 1);
        $invoices = Invoice::with('order')->where('customer_id', $customerId)->paginate($perPage, ['*'], 'page', $page);
        // return response()->json($invoices);
           return response()->json([
            [
                'invoiceId' => 'INV001',
                'product' => [
                    [
                        'productName' => 'Wireless Mouse',
                        'quantity' => 2,
                        'totalAmount' => 1299.99,
                    ],
                    [
                        'productName' => 'Laptop',
                        'quantity' => 1,
                        'totalAmount' => 1000,
                    ],
                ],
                'totalAmount' => 2299.99,
                'buyDate' => '2023-05-15',
                'status' => 'Paid',
            ],
            [
                'invoiceId' => 'INV002',
                'product' => [
                    [
                        'productName' => 'Wireless Mouse',
                        'quantity' => 3,
                        'totalAmount' => 1299.99,
                    ],
                ],
                'totalAmount' => 49.99,
                'buyDate' => '2023-05-18',
                'status' => 'Processing',
            ],
            [
                'invoiceId' => 'INV003',
                'product' => [
                    [
                        'productName' => 'Laptop Pro X',
                        'quantity' => 2,
                        'totalAmount' => 399.99,
                    ],
                ],
                'totalAmount' => 399.99,
                'buyDate' => '2023-05-20',
                'status' => 'Shipped',
            ],
        ]);
    }
     
    }

