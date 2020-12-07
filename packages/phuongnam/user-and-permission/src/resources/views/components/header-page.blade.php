<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ $currentPage }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">@lang('Home')</a></li>
                @forelse ($parentPage as $page)
                <li class="breadcrumb-item"><a href="{{ route($page['route']) }}">{{ $page['text'] }}</a></li>
                @empty
                @endforelse
                <li class="breadcrumb-item active">{{ $currentPage }}</li>
                </ol>
            </div>
        </div>
    </div>
</section>
