{{-- Editable table of {description, quantity, unit_price} rows, with a picker
     that inserts a package straight from the public pricing guide. Totals are
     recalculated live in admin.js; the server recomputes them on save. --}}
@php
    $packages = \App\Support\Site::packages();
@endphp
<div class="line-items" data-line-items data-currency-field="rf-currency" data-tax-field="rf-tax_rate">
    <div class="line-items-package">
        <select data-package-select>
            <option value="">Add a package from the pricing guide...</option>
            @foreach ($packages as $p)
                <option value="{{ $p['key'] }}" data-inr="{{ $p['INR'] }}" data-usd="{{ $p['USD'] }}" data-label="{{ $p['label'] }}" data-description="{{ $p['description'] }}">{{ $p['label'] }}</option>
            @endforeach
        </select>
        <button type="button" class="admin-secondary-btn" data-package-add disabled>Add</button>
    </div>

    <table class="line-items-table">
        <thead>
            <tr>
                <th>Description</th>
                <th style="width: 90px">Qty</th>
                <th style="width: 140px">Unit price</th>
                <th style="width: 130px">Amount</th>
                <th style="width: 40px"></th>
            </tr>
        </thead>
        <tbody data-line-rows>
            @foreach ($items as $i => $item)
                <tr data-line-row>
                    <td>
                        <input type="text" name="items[{{ $i }}][description]" value="{{ $item['description'] ?? '' }}" placeholder="e.g. Business website (12 pages)" data-field="description" />
                        @error('items.'.$i.'.description')<span class="field-error">{{ $message }}</span>@enderror
                    </td>
                    <td>
                        <input type="number" min="0" step="0.01" name="items[{{ $i }}][quantity]" value="{{ $item['quantity'] ?? 1 }}" data-field="quantity" />
                    </td>
                    <td>
                        <input type="number" min="0" step="0.01" name="items[{{ $i }}][unit_price]" value="{{ $item['unit_price'] ?? '' }}" data-field="unit_price" />
                        @error('items.'.$i.'.unit_price')<span class="field-error">{{ $message }}</span>@enderror
                    </td>
                    <td class="line-items-amount" data-line-amount></td>
                    <td>
                        <button type="button" class="line-items-remove" data-line-remove title="Remove line">×</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <button type="button" class="admin-link-btn" data-line-add>+ Add line</button>

    <div class="line-items-totals">
        <span>Subtotal</span>
        <strong data-total="subtotal"></strong>
        <span data-tax-label>GST / tax (0%)</span>
        <strong data-total="tax"></strong>
        <span>Total</span>
        <strong class="line-items-grand" data-total="total"></strong>
    </div>
</div>
