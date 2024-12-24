@extends('layouts.app')

@section('content')
<div class="container">
    <div class="fs-2 text-uppercase">{{ $tasker->user->code }} : {{ $tasker->user->username }}</div>
        <span>{{ $tasker->user->email }}</span> <br>
        <span>{{ $tasker->user->phone_number }}</span>


        <div class="row justify-content-center">
            @foreach ($taskerStatistics as $key => $value)
                <div class="col-md-3 pa-0 ma-0">
                    <statistics-card 
                    :title="'{{ $key }}'" 
                    :value="'{{ $value }}'" 
                    />
                </div>
            @endforeach
        </div>

        <div class="container mt-4">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">Managed Since</th>
                        <th scope="col">Code</th>
                        <th scope="col">Status</th>
                        <th scope="col">Owner</th>
                        <th scope="col">Provider</th>
                        <th scope="col">Owner Rate</th>
                        <th scope="col">Tasker Rate</th>
                        <th scope="col">Jobraq Rate</th>
                        <th scope="col">Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($managedAccounts as $account)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($account->created_at)->format("d M Y") }}</td>
                            <td>{{ $account->code }}</td>
                            <td>{{ $account->status }}</td>
                            <td>
                            {{ $account->user -> username }} <br>
                            {{ $account->user -> email }} <br>
                            {{ $account->user -> phone_number }} <br>
                            </td>
                            <td>{{ $account->provider }}</td>
                            <td>{{ $account->owner_rate . "%"}}</td>
                            <td>{{ $account->tasker_rate . "%"}}</td>
                            <td>{{ $account->jobraq_rate . "%"}}</td>
                            <td>{{ $account->debit_revenue_sum ? $account->debit_revenue_sum : 0 }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No Accounts found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination links -->
            <div class="d-flex justify-content-center">
            {{ $managedAccounts->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
@endsection