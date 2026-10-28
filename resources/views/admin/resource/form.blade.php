{{--
    Add/edit form panel. Field definitions:
      ['name', 'label', 'type' => text|email|number|date|password|select|textarea|checkbox|items,
       'options' => [value => label] or [['value','label','parent']], 'placeholder', 'required',
       'full', 'help', 'rows', 'step', 'min', 'checkboxLabel', 'dependsOn' => 'client_id', 'strict' => bool]
--}}
@if ($formOpen)
    @php
        $useOld = old('_form') === 'resource';
        $val = fn ($name) => $useOld ? old($name) : ($values[$name] ?? '');
        $isEdit = (bool) $editing;
    @endphp
    <div class="admin-panel resource-form-panel" id="resource-form">
        <div class="admin-panel-header">
            <h2>{{ $isEdit ? 'Edit '.$formTitle : 'New '.$formTitle }}</h2>
            <a href="{{ $listUrl }}" class="admin-panel-link">Cancel</a>
        </div>
        @if ($errors->any())
            <div class="form-alert form-alert-error">{{ $errorMessage ?? 'Please check the highlighted fields and try again.' }}</div>
        @endif
        <form method="POST" action="{{ $isEdit ? $updateUrl : $storeUrl }}" class="resource-form" novalidate data-submitting-label="Saving...">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif
            <input type="hidden" name="_form" value="resource" />
            <input type="hidden" name="_back" value="{{ $listUrl }}" />
            <div class="resource-form-grid">
                @foreach ($fields as $field)
                    @include('admin.resource.field', ['field' => $field, 'value' => $val($field['name'])])
                @endforeach
            </div>
            <div class="admin-form-actions">
                <a href="{{ $listUrl }}" class="admin-secondary-btn">Cancel</a>
                <button type="submit" class="admin-settings-btn">{{ $isEdit ? 'Save changes' : 'Create '.$formTitle }}</button>
            </div>
        </form>
    </div>
@endif
