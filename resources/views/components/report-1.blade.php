@extends('layout')
@section('content')
    <div class="container mt-3">
        <h3>Head Type Report</h3>
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Head Type</th>
                    <th>Account Head</th>
                    <th>Total Amount</th>
                </tr>
                </thead>
                <tbody>
                @foreach($head_types as $key => $head_type)
                    @php
                        $total_amount = number_format(
                            (array_key_exists($key, $account_heads->toArray())) ?
                            $account_heads[$key]->sum('total_amount'): 0, 2);
                    @endphp
                    <tr>
                        <th>{{ $head_type }}</th>
                        <td></td>
                        <th>{{$total_amount}}</th>
                    </tr>
                    @foreach($account_heads[$key] ?? [] as $account_head)
                        <tr>
                            <td></td>
                            <td>{{ $account_head->name }}</td>
                            <td>{{ number_format($account_head->total_amount, 2) }}</td>
                        </tr>
                    @endforeach
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
