<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\QuotationController;
use Illuminate\Http\Request;

/**
 * Printable invoice / quotation without the admin chrome; the browser's
 * "Save as PDF" produces the PDF.
 */
class PrintController extends Controller
{
    public function __invoke(Request $request, string $kind, $id)
    {
        abort_unless(in_array($kind, ['invoice', 'quotation'], true), 404);

        $isInvoice = $kind === 'invoice';
        abort_unless($request->user()->canAccess($isInvoice ? 'invoices' : 'quotations'), 403, 'You do not have access to this.');

        $doc = $isInvoice
            ? app(InvoiceController::class)->show($id)
            : app(QuotationController::class)->show($id);

        return view('admin.print', ['doc' => $doc, 'isInvoice' => $isInvoice]);
    }
}
