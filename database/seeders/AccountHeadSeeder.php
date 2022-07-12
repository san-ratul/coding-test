<?php

namespace Database\Seeders;

use App\Models\AccountHead;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountHeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //create 8 account heads without using factory
        $this->createAccountHeads();

    }

    private function createAccountHeads()
    {
        //create 8 account heads without using factory
        $accountHeads = [
            [
                'name' => 'Account Head 1',
                'head_type' => 'expense',
            ],
            [
                'name' => 'Account Head 2',
                'head_type' => 'asset',
            ],
            [
                'name' => 'Account Head 3',
                'head_type' => 'liability',
            ],
            [
                'name' => 'Account Head 4',
                'head_type' => 'liability',
            ],
            [
                'name' => 'Account Head 5',
                'head_type' => 'income',
            ],
            [
                'name' => 'Account Head 6',
                'head_type' => 'asset',
            ],
            [
                'name' => 'Account Head 7',
                'head_type' => 'liability',
            ],
            [
                'name' => 'Account Head 8',
                'head_type' => 'income',
            ],
        ];
        foreach ($accountHeads as $accountHead) {
            AccountHead::create($accountHead);
        }
    }
}
