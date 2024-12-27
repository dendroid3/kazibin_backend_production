<!-- Include Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<!-- Include Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>

<script>
    function showEditForm() {
        console.log(typeof bootstrap); 
        var editFormModal = new bootstrap.Modal(document.getElementById('editFormModal'));
        editFormModal.show();
    }

    function validateRates() {
        const ownerRate = parseInt(document.getElementById('owner_rate').value) || 0;
        const taskerRate = parseInt(document.getElementById('tasker_rate').value) || 0;
        const jobraqRate = parseInt(document.getElementById('jobraq_rate').value) || 0;
        const total = ownerRate + taskerRate + jobraqRate;

        if (total !== 100) {
            document.getElementById('rateError').style.display = 'block';
        } else {
            document.getElementById('rateError').style.display = 'none';
        }
    }
</script>
@extends('layouts.app')

@section('content')


<?php
$countries = ["USA", "UK", "Canada", "Kenya", "Afghanistan", "Albanian", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas (the)", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia (Plurinational State of)", "Bonaire, Sint Eustatius and Saba", "Bosnia and Herzegovina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory (the)", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cabo Verde", "Cambodia", "Cameroon", "Cayman Islands (the)", "Central African Republic (the)", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands (the)", "Colombia", "Comoros (the)", "Congo (the Democratic Republic of the)", "Congo (the)", "Cook Islands (the)", "Costa Rica", "Croatia", "Cuba", "Curaçao", "Cyprus", "Czechia", "Côte d'Ivoire", "Denmark", "Djibouti", "Dominica", "Dominican Republic (the)", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Eswatini", "Ethiopia", "Falkland Islands (the) [Malvinas]", "Faroe Islands (the)", "Fiji", "Finland", "France", "French Guiana", "French Polynesia", "French Southern Territories (the)", "Gabon", "Gambia (the)", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guernsey", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard Island and McDonald Islands", "Holy See (the)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Isle of Man", "Israel", "Italy", "Jamaica", "Japan", "Jersey", "Jordan", "Kazakhstan", "Kiribati", "Korea (the Democratic People's Republic of)", "Korea (the Republic of)", "Kuwait", "Kyrgyzstan", "Lao People's Democratic Republic (the)", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg", "Macao", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands (the)", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia (Federated States of)", "Moldova (the Republic of)", "Monaco", "Mongolia", "Montenegro", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands (the)", "New Caledonia", "New Zealand", "Nicaragua", "Niger (the)", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands (the)", "Norway", "Oman", "Pakistan", "Palau", "Palestine, State of", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines (the)", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Republic of North Macedonia", "Romania", "Russian Federation (the)", "Rwanda", "Réunion", "Saint Barthélemy", "Saint Helena, Ascension and Tristan da Cunha", "Saint Kitts and Nevis", "Saint Lucia", "Saint Martin (French part)", "Saint Pierre and Miquelon", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Serbia", "Seychelles", "Sierra Leone", "Singapore", "Sint Maarten (Dutch part)", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "South Sudan", "Spain", "Sri Lanka", "Sudan (the)", "Suriname", "Svalbard and Jan Mayen", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Timor-Leste", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands (the)", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates (the)", "United States Minor Outlying Islands (the)", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela (Bolivarian Republic of)", "Viet Nam", "Virgin Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna", "Western Sahara", "Yemen", "Zambia", "Zimbabwe", "Åland Islands" ]
?>

<div class="container">
    <div class="fs-2 text-uppercase">{{ __('Managed Account') }}</div>

    <div class="row justify-content-center">
        @foreach ($account_statistics as $key => $value)
            <div class="col-md-3 pa-0 ma-0">
                <statistics-card 
                :title="'{{ $key }}'" 
                :value="'{{ $value }}'" 
                />
            </div>
        @endforeach
    </div>
    <div class="container mt-4">
        <h2 class="text-center mb-3">
            {{ "Account Details" }}
        </h2>
        <div class="d-flex justify-content-center">
            <div>
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
                                <button class="btn btn-success btn-sm" onclick="showEditForm()">Edit</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Edit Form Modal -->
    <div class="modal fade" id="editFormModal" tabindex="-1" aria-labelledby="editFormModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editFormModalLabel">Edit Account Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editAccountForm" method="POST" action="/admin/managed_account/update">
                        @csrf
                        <input type="hidden" name="account_id" value="{{ $account->id }}">
                        <div class="mb-3">
                            <label for="code" class="form-label">Code</label>
                            <input type="text" class="form-control" id="code" name="code" value="{{ $account->code }}">
                        </div>
                        <div class="mb-3">
                            <label for="provider" class="form-label">Provider</label>
                            <input type="text" class="form-control" id="provider" name="provider" value="{{ $account->provider }}">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ $account->email }}">
                        </div>
                        <div class="mb-3">
                            <label for="tasker" class="form-label">Tasker</label>
                            <input type="text" class="form-control" id="tasker" name="tasker" value="{{ $account->tasker->user->code ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="active" {{ $account->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="closed" {{ $account->status == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="proxy" class="form-label">Proxy</label>
                            <select class="form-control" id="proxy" name="proxy">
                                @foreach ($countries as $country)
                                    <option value="{{ $country }}" {{ $account->proxy == $country ? 'selected' : '' }}>{{ $country }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="payday" class="form-label">Payday</label>
                            <input type="number" class="form-control" id="payday" name="payday" value="{{ $account->payday }}" min="1" max="30">
                        </div>
                        @if(is_null($account->owner_rate))
                            <div class="mb-3">
                                <label for="owner_rate" class="form-label">Owner Rate</label>
                                <input type="number" class="form-control" id="owner_rate" name="owner_rate" value="{{ $account->owner_rate }}" max="100" oninput="validateRates()">
                            </div>
                            <div class="mb-3">
                                <label for="tasker_rate" class="form-label">Tasker Rate</label>
                                <input type="number" class="form-control" id="tasker_rate" name="tasker_rate" value="{{ $account->tasker_rate }}" max="100" oninput="validateRates()">
                            </div>
                            <div class="mb-3">
                                <label for="jobraq_rate" class="form-label">Jobraq Rate</label>
                                <input type="number" class="form-control" id="jobraq_rate" name="jobraq_rate" value="{{ $account->jobraq_rate }}" max="100" oninput="validateRates()">
                            </div>
                            <div id="rateError" class="text-danger" style="display: none;">The sum of Owner Rate, Tasker Rate, and Jobraq Rate must be 100.</div>
                        @endif
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container mt-4">
        <h2 class="text-center mb-3">
            {{ "Revenue List" }}
        </h2>
        <div class="d-flex justify-content-center">
            <div>
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">Date</th>
                            <th scope="col">Type</th>
                            <th scope="col">Description</th>
                            <th scope="col">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($account -> revenue as $revenue)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($revenue->created_at)->format('H:i, d/m/Y') }}</td>
                                <td>{{ $revenue -> type }}</td>
                                <td>{{ $revenue -> description }}</td>
                                <td>{{ $revenue -> amount }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="text-center">No revenue found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-center">
                    {{ $account->revenue->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
        
    </div>
</div>

@endsection
