{{-- Page heading with the icon of the current menu item. $icon overrides it. --}}
<div class="admin-page-title">
    <span class="admin-page-icon">@include('admin.partials.icon', ['name' => $icon ?? \App\Support\AdminNav::currentIcon(), 'class' => ''])</span>
    <div>
        <h1>{{ $title }}</h1>
        @if (!empty($subtitle))
            <p>{{ $subtitle }}</p>
        @endif
    </div>
</div>
