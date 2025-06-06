@include('layouts.app')
<meta name="viewport"
      content="width=device-width, initial-scale=1"> <!-- Ensures optimal rendering on mobile devices -->
@include('layouts.header')
<div class="siddhi-checkout siddhi-checkout-payment">
    <div class="container position-relative"> 
        <div class="py-5 row">
            <div class="pb-2 align-items-starrt sec-title col">
                <h2 class="m-0">{{trans('lang.pay_with_paypal')}}</h2>
                <p class="sub-title">{{trans('lang.payment_tagline_message')}}</p>
            </div>
            <div class="col-md-12 mb-3">
                <div>
                    <div class="siddhi-cart-item mb-3 rounded shadow-sm bg-white overflow-hidden">
                        <div class="siddhi-cart-item-profile bg-white p-3">
                            <div class="card card-default payment-wrap">
                                <table class="payment-table m-4">
                                    <thead>
                                    <tr>
                                        <th class="p-1">
                                            {{ trans('lang.pay_total_amount') }} : {{ $formatted_price }}
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <div id="paypal-button-container"></div>
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
<script src="https://www.paypal.com/sdk/js?client-id=<?php echo $paypalKey; ?>&currency=USD"></script>
<script>
    paypal.Buttons({
        // Sets up the transaction when a payment button is clicked
        createOrder: function (data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: '<?php echo $amount; ?>' // Can reference variables or functions. Example: `value: document.getElementById('...').value`
                    }
                }]
            });
        },
        // Finalize the transaction after payer approval
        onApprove: function (data, actions) {
            return actions.order.capture().then(function (orderData) {
                // Successful capture! For dev/demo purposes:
                if (orderData.status == "COMPLETED") {
                    $.ajax({
                        type: 'POST',
                        url: "<?php echo route('process-paypal'); ?>",
                        data: {_token: '<?php echo csrf_token() ?>'},
                        success: function (data) {
                            data = JSON.parse(data);
                            if (data.status == true) {
                                window.location.href = '<?php echo route('success'); ?>';
                            }
                        },
                        error: function (XMLHttpRequest, textStatus, errorThrown) {
                        }
                    });
                }
                console.log('Capture result', orderData, JSON.stringify(orderData, null, 2));
                var transaction = orderData.purchase_units[0].payments.captures[0];
            });
        }
    }).render('#paypal-button-container');
</script>
@include('layouts.footer')
@include('layouts.nav')
