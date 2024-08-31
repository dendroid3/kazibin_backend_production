<?php

namespace App\Services\Account;

use App\Models\Account;

class AccountsService
{
    public function create() {}

    public function getSomeForDisplay() {
        $accounts = Account::query() -> where('display', true) -> take(10) -> get();

        foreach ($accounts as $account) {
            $account -> User;
        }

        return $accounts;
    }

    public function getAllPaginated() {
        $accounts = Account::query() -> where('display', true) -> paginate(10);

        foreach ($accounts as $account) {
            $account -> User;
        }

        return $accounts;
    }
}
