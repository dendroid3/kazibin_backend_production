@extends('layouts.app')

@section('content')
<div class="container">
    <div class="fs-2 text-uppercase">{{ __('Dashboard') }}</div>
    <div class="mb-4 text-uppercase">Welcome {{ $user -> role . " " . $user ->username }}</div>

    @if($user -> role == "Admin" || $user -> role == "admin")
    <div class="row justify-content-center">
        @foreach ($statistics as $key => $value)
            <div class="col-md-3 pa-0 mb-4">
                <statistics-card 
                :title="'{{ $key }}'" 
                :value="'{{ $value }}'" 
                :user_role="`{{ $user -> role }}`" 
                :url="'/' + '{{ $user->role }}' + '/' + '{{ $key }}'"
                />
            </div>
        @endforeach
    </div>
    @elseif($user -> role == "Tasker" || $user -> role == "tasker")
    <div class="row justify-content-center">
        @foreach ($statistics as $key => $value)
            <div class="col-md-3 pa-0 mb-4">
                <statistics-card 
                :title="'{{ $key }}'" 
                :value="'{{ $value }}'" 
                :user_role="`{{ $user -> role }}`" 
                :url="'/' + '{{ $user->role }}' + '/' + '{{ $key }}'"
                />
            </div>
        @endforeach

    </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="cardsd">

                <div class="card-body">
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
