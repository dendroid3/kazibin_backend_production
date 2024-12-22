@extends('layouts.app')

@section('content')
<div class="container">
    <div class="fs-2 text-uppercase">{{ __('Taskers') }}</div>

    <div class="row justify-content-center">
        @foreach ($taskers_statistics as $key => $value)
            <div class="col-md-3 pa-0 ma-0">
                <statistics-card 
                :title="'{{ $key }}'" 
                :value="'{{ $value }}'" 
                :user_role="`{{ $user -> role }}`" 
                :url="'?status=' + '{{ $key }}'"
                />
            </div>
        @endforeach
    </div>
    <div class="container mt-4">
        <h2 class="text-center mb-3">
            {{ request()->has('status') ? ucfirst(request()->get('status')) . ' Taskers List: ' : 'All Taskers List' }}
        </h2>
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th scope="col">Username</th>
                    <th scope="col">Email</th>
                    <th scope="col">Status</th>
                    <th scope="col">Score</th>
                    <th scope="col">Accounts</th>
                    <th scope="col">Total Revenue</th>
                    <th scope="col">Joined</th>
                    <th scope="col">Last Login</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($taskers as $tasker)
                    <tr>
                        <td>{{ $tasker->username }}</td>
                        <td>{{ $tasker->email }}</td>
                        <td>{{ $tasker->tasker->status }}</td>
                        <td>{{ $tasker->tasker->score }}</td>
                         <td>{{ $tasker['tasker']['managedAccounts'] -> count() }}</td>
                        <td>{{ $tasker->total_revenue }}</td>
                        <td>{{ \Carbon\Carbon::parse($tasker->tasker->created_at)->diffForHumans() }}</td>
                        <td>{{ \Carbon\Carbon::parse($tasker->last_activity)->diffForHumans() }}</td>
                        <td>
                        <a class="btn btn-success btn-sm" href="/admin/tasker?tasker_id={{$tasker->tasker->id}}">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">No taskers found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination links -->
        <div class="d-flex justify-content-center">
            {{ $taskers->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endsection
