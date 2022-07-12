<?php

namespace Database\Seeders;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Testing\WithFaker;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create 10000 transactions for last 3 months 5000 with debit and 5000 with credit
        $this->createTransactions();
    }

    private function createTransactions()
    {
        // create 10000 transactions for last 3 months 5000 with debit and 5000 with credit
        for ($i = 0; $i < 5000; $i++) {
            $debit = random_int(10, 1000);
            Transaction::create(
                [
                    'account_head_id' => random_int(1, 8),
                    'date' => Carbon::today()->subDays(rand(0, 180)),
                    'debit' => $debit,
                ]
            );
        }
        for ($i = 0; $i < 5000; $i++) {
            $credit = random_int(10, 1000);
            Transaction::create(
                [
                    'account_head_id' => random_int(1, 8),
                    'date' => Carbon::today()->subDays(rand(0, 180)),
                    'credit' => $credit,
                ]
            );
        }

    }
}
