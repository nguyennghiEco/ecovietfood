@include('layouts.app')
@include('layouts.header')
<div class="siddhi-checkout siddhi-checkout-payment">
    <div class="container position-relative">
        <div class="py-5 row">
            <div class="pb-2 align-items-starrt sec-title col">
                <h2 class="m-0">{{trans('lang.pay_with_razorpay')}}</h2>
                <p class="sub-title">{{trans('lang.payment_tagline_message')}}</p>
            </div>
            <div class="col-md-12 mb-3">
                <div>
                    <div class="siddhi-cart-item mb-3 rounded shadow-sm bg-white overflow-hidden">
                        <div class="siddhi-cart-item-profile bg-white p-3">
                            <div class="card card-default payment-wrap">
                                <table class="payment-table m-4">
                                    <tbody>
                                    <tr>
                                        <td>
                                            {{ trans('lang.pay_total_amount') }} : {{ $formatted_price }}
                                        </td>
                                        <td class="text-right payment-button">
                                            <form action="{{ route('razorpaypayment') }}" method="POST">
                                                @csrf
                                                <script src="https://checkout.razorpay.com/v1/checkout.js"
                                                        data-key="{{ $razorpayKey }}"
                                                        data-amount="{{$amount*100}}"
                                                        data-buttontext="Pay {{ $formatted_price }}"
                                                        data-name="{{env('APP_NAME', 'Foodie')}}"
                                                        data-description="Rozerpay"
                                                        data-image="https://www.itsolutionstuff.com/frontTheme/images/logo.png"
                                                        data-prefill.name="{{$authorName}}"
                                                        data-prefill.email="{{$email}}"
                                                        data-theme.color="#ff7529">
                                                </script>
                                            </form>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('layouts.footer')
@include('layouts.nav')
<script>
    $(".razorpay-payment-button").addClass('btn btn-primary');
</script>
