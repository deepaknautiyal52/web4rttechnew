@php
    $name = $field['name'];
    $type = $field['type'] ?? 'text';
    $id = 'rf-'.$name;
    $value = is_bool($value) ? ($value ? '1' : '0') : $value;
@endphp
<div class="admin-form-group{{ !empty($field['full']) ? ' resource-field-full' : '' }}">
    @if ($type !== 'checkbox')
        <label for="{{ $id }}">{{ $field['label'] }}{{ !empty($field['required']) ? ' *' : '' }}</label>
    @endif

    @switch($type)
        @case('select')
            <select id="{{ $id }}" name="{{ $name }}"
                @if (!empty($field['dependsOn'])) data-depends-on="rf-{{ $field['dependsOn'] }}" @if (!empty($field['strict'])) data-strict @endif @endif>
                @if (array_key_exists('placeholder', $field))
                    <option value="">{{ $field['placeholder'] }}</option>
                @endif
                @foreach ($field['options'] as $optValue => $opt)
                    @php
                        $v = is_array($opt) ? $opt['value'] : $optValue;
                        $l = is_array($opt) ? $opt['label'] : $opt;
                    @endphp
                    <option value="{{ $v }}" @if (is_array($opt) && array_key_exists('parent', $opt)) data-parent="{{ $opt['parent'] }}" @endif @selected((string) $value === (string) $v)>{{ $l }}</option>
                @endforeach
            </select>
            @break

        @case('textarea')
            <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $field['rows'] ?? 3 }}" placeholder="{{ $field['placeholder'] ?? '' }}">{{ $value }}</textarea>
            @break

        @case('checkbox')
            <input type="hidden" name="{{ $name }}" value="0" />
            <label class="admin-checkbox">
                <input type="checkbox" name="{{ $name }}" value="1" @checked((string) $value === '1') />
                {{ $field['checkboxLabel'] ?? $field['label'] }}
            </label>
            @break

        @case('items')
            @include('admin.resource.line-items', ['items' => is_array($value) && count($value) ? array_values($value) : [['description' => '', 'quantity' => 1, 'unit_price' => '']]])
            @break

        @default
            <input id="{{ $id }}" type="{{ $type }}" name="{{ $name }}" value="{{ $type === 'password' ? '' : $value }}"
                placeholder="{{ $field['placeholder'] ?? '' }}"
                @if ($type === 'number') min="{{ $field['min'] ?? '0' }}" step="{{ $field['step'] ?? '0.01' }}" @endif />
    @endswitch

    @if (!empty($field['help']))
        <span class="resource-field-help">{{ $field['help'] }}</span>
    @endif
    @error($name)
        <span class="field-error">{{ $message }}</span>
    @enderror
</div>
