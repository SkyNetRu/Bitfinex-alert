<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet"/>

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz"
            crossorigin="anonymous"></script>
    <style>
        .form-settings {
            max-width: 50vw;
            padding: 1rem;
        }
    </style>

</head>
<body class="d-flex align-items-center py-4 bg-body-tertiary min-vh-100" style="">
<main class="form-settings w-100 m-auto">
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <h1>Minimum Funding Rate</h1>
    <form action="{{route('update-settings')}}" method="post">
        <div class="form-floating">
            <input type="text" value="{{ $settings ? $settings->currency : '' }}" name="currency"
                   class="form-control"
                   id="fCurrency"
                   placeholder="fUSD"/>
            <label for="fCurrency">Currency</label>
        </div>

        <div class="form-floating">
            <input type="text" value="{{ $settings ? $settings->rate_alert : '' }}" name="rate_alert"
                   class="form-control"
                   id="rate_alert"
                   placeholder="0.04"/>
            <label for="rate_alert">Rate Alert</label>
        </div>
        @csrf
        <button class="btn btn-primary w-100 py-2 mt-2" type="submit">Update</button>
    </form>

    <br/>

    <h1>Set Order</h1>
    <form action="{{route('submit-order')}}" method="post">
        <div class="form-floating">
            <input type="text" value="tUSTUSD" name="currency"
                   class="form-control"
                   id="oCurrency"
                   placeholder="tUSTUSD"/>
            <label for="oCurrency">Currency</label>
        </div>

        <div class="form-floating">
            <input type="text" value="1.0011" name="price"
                   class="form-control"
                   id="price"
                   placeholder="1.0011"/>
            <label for="price">Price</label>
        </div>

        <div class="form-floating">
            <input type="text" value="1000" name="amount"
                   class="form-control"
                   id="amount"
                   placeholder="1000"/>
            <label for="amount">Amount</label>
        </div>

        <div class="form-floating">
            <input type="text" value="EXCHANGE LIMIT" name="type"
                   class="form-control"
                   id="orderType"
                   placeholder="EXCHANGE LIMIT"/>
            <label for="orderType">Order Type</label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="radio" name="isSell" id="sellOrBuy1" checked>
            <label class="form-check-label" for="sellOrBuy1">
                Sell
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="isSell" id="sellOrBuy2">
            <label class="form-check-label" for="sellOrBuy2">
                Buy
            </label>
        </div>
        @csrf
        <button class="btn btn-primary w-100 py-2 mt-2" type="submit">Submit order</button>
    </form>
</main>
</body>
</html>
