{{-- page name vs breadcrumb --}}
@php
    $segments = Request::segments();
    $breadcrumbItems = [];

    for ($i = 1; $i <= count($segments); $i++) {
        $label = ucwords(str_replace('-', ' ', $segments[$i - 1]));
        $url = url(implode('/', array_slice($segments, 0, $i)));
        $breadcrumbItems[] = ['label' => $label, 'url' => $url];
    }
@endphp

<div class="content-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">{{ View::getSection('title') ?? 'Dashboard' }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    @forelse ($breadcrumbItems ?? [] as $breadcrumbItem)
                        <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}">
                            @if ($loop->last)
                                {{ ucfirst($breadcrumbItem['label'] ?? 'label') }}
                            @else
                                <a
                                    href="{{ $breadcrumbItem['url'] ?? '#' }}">{{ ucfirst($breadcrumbItem['label'] ?? 'label') }}</a>
                            @endif
                        </li>
                    @empty
                        <li class="breadcrumb-item active">Label</li>
                    @endforelse
                </ol>
            </div>
        </div>
    </div>
</div>
