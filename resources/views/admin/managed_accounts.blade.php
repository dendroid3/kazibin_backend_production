@extends('layouts.app')

@section('content')
<div>

    <div class="container">
            
        <div class="fs-2 text-uppercase">{{ __('Managed Accounts') }}</div>

        <div class="row justify-content-center">
            @foreach ($managed_accounts_statistics as $key => $value)
                <div class="col-md-3 pa-0 ma-0">
                    <statistics-card 
                    :title="'{{ $key }}'" 
                    :value="'{{ $value }}'" 
                    :url="'?status=' + '{{ $key }}'"
                    />
                </div>
            @endforeach
        </div>

    </div>

    <div class="mx-2 mt-4">
        <h2 class="text-center mb-3">
            {{ request()->has('status') ? ucfirst(request()->get('status')) . ' Managed Accounts List: ' : 'All Managed Accounts List' }}
        </h2>
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th scope="col">Code</th>
                    <th scope="col">Provider</th>
                    <th scope="col">Email</th>
                    <th scope="col">Status</th>
                    <th scope="col">Proxy</th>
                    <th scope="col">Owner</th>
                    <th scope="col">Tasker</th>
                    <th scope="col">O/R</th>
                    <th scope="col">T/R</th>
                    <th scope="col">J/R</th>
                    <th scope="col">Total Earning</th>
                    <th scope="col">Managed Since</th>
                    <th scope="col">Payday</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($accounts as $account)
                    <tr>
                        <td>{{ $account -> code }}</td>
                        <td>{{ $account -> provider }}</td>
                        <td>{{ $account -> email }}</td>
                        <td>{{ $account -> status }}</td>
                        <td>{{ $account->proxy ?? 'None' }}</td>
                        <td>
                        {{ $account -> user -> code }} : {{ $account -> user -> username }} <br>
                        {{ $account -> user -> email }} <br>
                        {{ $account -> user -> phone_number }} <br>
                        </td>
                        @if($account -> tasker)
                            <td>
                            {{ $account -> tasker -> user -> code }} : {{ $account -> tasker -> user -> username }} <br>
                            {{ $account -> tasker -> user -> email }} <br>
                            {{ $account -> tasker -> user -> phone_number }} <br>
                            </td>
                        @else
                            <td>Unassigned</td>
                        @endif
                        <td>{{ $account -> owner_rate ?? "N/A" }}</td>
                        <td>{{ $account -> tasker_rate ?? "N/A" }}</td>
                        <td>{{ $account -> jobraq_rate ?? "N/A" }}</td>
                        <td>{{ $account -> debit_revenue_sum ?? 0 }}</td>
                        <td>{{ \Carbon\Carbon::parse($account->created_at)->diffForHumans() }}</td>
                        <td>{{ $account -> payday }}</td>
                        <td>
                            <a class="btn btn-success btn-sm" href="/admin/managed_account?account_id={{$account->id}}">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="13" class="text-center">No accounts found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination links -->
        <div class="d-flex justify-content-center">
            {{ $accounts->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>

@endsection
