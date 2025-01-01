@extends('layouts.app')

@section('content')
<div>

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
                    <th scope="col">O/R</th>
                    <th scope="col">T/R</th>
                    <th scope="col">J/R</th>
                    <th scope="col">Total Earning</th>
                    <th scope="col">Managed Since</th>
                    <th scope="col">Payday</th>
                    <th scope="col">Actions</th>
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
                        <td>{{ $account -> owner_rate ?? "N/A" }}</td>
                        <td>{{ $account -> tasker_rate ?? "N/A" }}</td>
                        <td>{{ $account -> jobraq_rate ?? "N/A" }}</td>
                        <td>{{"$"}}{{ $account -> debit_revenue_sum ?? 0 }}</td>
                        <td>{{ \Carbon\Carbon::parse($account->created_at)->diffForHumans() }}</td>
                        <td>{{ $account -> payday }}</td>
                        <td>
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addEarningModal-{{ $account->id }}">
                            Add Earning
                        </button>
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addEarningModal-{{ $account->id }}">
                            Create Invoice
                        </button>
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addEarningModal-{{ $account->id }}">
                            View
                        </button>

                        <!-- Modal -->
                        <div class="modal fade" id="addEarningModal-{{ $account->id }}" tabindex="-1" aria-labelledby="addEarningModalLabel-{{ $account->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addEarningModalLabel-{{ $account->id }}">Add Earning for {{ $account->code }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="/tasker/managed_account/add_earning" method="POST">
                                            @csrf
                                            <input type="hidden" name="managed_account_id" value="{{ $account->id }}">
                                            <div class="mb-3">
                                                <label for="amount" class="form-label">Amount ($)</label>
                                                <input type="number" class="form-control" id="amount" name="amount" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="description" class="form-label">Description</label>
                                                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Add Earning</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
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
        </div

</div>
@endsection