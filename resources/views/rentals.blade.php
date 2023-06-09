@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> {{ session('success') }}.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close" @click="id = ''">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div x-data="{ id: null }">
        <section>
            <div class="container">
                <div class="text-center">
                    <h1>Rentals</h1>

                    <br>

                    <p class="lead">These cars that you rented.</p>
                </div>
            </div>
        </section>

        <section class="section-background">
            <div class="container">
                <div class="row">
                    @foreach ($rentals as $rental)
                        <div class="col-md-4 col-sm-4">
                            <div class="courses-thumb courses-thumb-secondary">
                                <div class="courses-top">
                                    <div class="courses-image">
                                        <img src="@if ($rental->photo) {{ asset('storage/' . $rental->photo) }}
                                    @else
                                    {{ asset('images/product-1-720x480.jpg') }} @endif "
                                            class="img-responsive" alt="">
                                    </div>
                                    <div class="courses-date">
                                        <span title="passegengers"><i class="fa fa-user"></i>
                                            {{ $rental->seats }}</span>
                                        <span title="luggages"><i class="fa fa-briefcase"></i>
                                            {{ $rental->luggages }}</span>
                                        <span title="doors"><i class="fa fa-sign-out"></i> {{ $rental->doors }}</span>
                                        <span title="transmission"><i class="fa fa-cog"></i>
                                            {{ str($rental->transmission)->substr(0, 1)->upper() }}</span>
                                    </div>
                                </div>

                                <div class="courses-detail">
                                    <h3><a href="{{ route('cars.index') }}">{{ str($rental->size)->ucfirst() }}:
                                            {{ str($rental->name)->ucfirst() }}</a></h3>
                                    <p class="lead"><small>Tariff: </small>
                                        <strong>{{ money($rental->tariff, 'IDR', true) }}</strong>
                                        <small>per
                                            day</small>
                                    </p>
                                    <h4>Details</h4>
                                    <p>Information: {{ str($rental->information) }}</p>
                                    <p>Rent start on
                                        <strong>{{ $rental->start_date->format('d M Y') }}</strong> at
                                        <strong>{{ $rental->pickup_location }}</strong>
                                    </p>
                                    <p>Please return before or on
                                        <strong>{{ $rental->end_date->format('d M Y') }}</strong> at
                                        <strong>{{ $rental->return_location }}</strong>
                                    </p>
                                    @php
                                        $subtotal = ($rental->start_date->diffInDays($rental->end_date) + 1) * intval($rental->tariff);
                                        $overtimePay = 0;
                                    @endphp
                                    @if ($rental->end_date->diffInDays(now()) > 0 && $rental->end_date->isPast())
                                        @php
                                            $daysPassed = $rental->end_date->diffInDays(now());
                                            $overtimePay = 0.1 * $subtotal * $daysPassed;
                                        @endphp
                                        <p class="text-danger font-italic">You already past {{ $daysPassed }} days from
                                            that date (fine: {{ money($overtimePay, 'IDR', true) }})</p>
                                        <p class="fs-4">
                                            Subtotal:
                                            {{ money($subtotal, 'IDR', true) }}
                                        </p>
                                    @endif
                                    <p class="fs-4">
                                        Total:
                                        {{ money($subtotal + $overtimePay, 'IDR', true) }}
                                    </p>
                                </div>

                                <div class="courses-info">
                                    <button type="button" data-toggle="modal" data-target=".bs-example-modal"
                                        class="section-btn btn btn-primary btn-block"
                                        @click="id = '{{ $rental->id }}';car = {{ $rental }}">Return
                                        Now</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <div class="modal fade bs-example-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document" @click.outside="id = ''">
                <div class="modal-content">
                    <div class="modal-header">

                        <button type="button" class="close" data-dismiss="modal" @click="id = ''" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="gridSystemModalLabel">Book Now</h4>

                    </div>

                    <div class="modal-body">
                        <h1 class="text-center">Are you sure want to return?</h1>
                    </div>

                    <form :action="`/rentals/${id}`" method="POST" class="modal-footer">
                        @method('DELETE')
                        @csrf
                        <button type="button" class="section-btn btn btn-danger" data-dismiss="modal" @click="id = ''"
                            aria-label="Close">
                            Cancel
                        </button>
                        <button type="submit" class="section-btn btn btn-primary">Yes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
