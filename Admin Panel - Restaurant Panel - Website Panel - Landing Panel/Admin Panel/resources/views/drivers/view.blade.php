@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor restaurantTitle">{{ trans('lang.driver_plural') }}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{!! route('drivers') !!}">{{ trans('lang.driver_plural') }}</a></li>
                    <li class="breadcrumb-item active">{{ trans('lang.restaurant_details') }}</li>
                </ol>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="resttab-sec">
                        <div class="menu-tab">
                            <ul>
                                <li class="active">
                                    <a href="{{ route('drivers.view', $id) }}">{{ trans('lang.tab_basic') }}</a>
                                </li>
                                <li>
                                    <a href="{{ route('orders') }}?driverId={{ $id }}">{{ trans('lang.tab_orders') }}</a>
                                </li>
                                <li>
                                    <a href="{{ route('driver.payout', $id) }}">{{ trans('lang.tab_payouts') }}</a>
                                </li>
                                <li>
                                    <a href="{{ route('payoutRequests.drivers.view', $id) }}">{{ trans('lang.tab_payout_request') }}</a>
                                </li>
                                <li>
                                    <a href="{{ route('users.walletstransaction', $id) }}">{{ trans('lang.wallet_transaction') }}</a>
                                </li>
                            </ul>
                        </div>
                        <div class="row">

                            <div class="col-md-3">
                                <div class="card card-box-with-icon bg--2">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <div class="card-box-with-content">
                                            <h4 class="text-dark-2 mb-1 h4 rest_active_count" id="total_earnings">$0.00</h4>
                                            <p class="mb-0 small text-dark-2">{{ trans('lang.dashboard_total_earnings') }}</p>
                                        </div>
                                        <span class="box-icon ab"><img src="{{ asset('images/total_earning.png') }}"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card card-box-with-icon bg--1">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <div class="card-box-with-content">
                                            <h4 class="text-dark-2 mb-1 h4 rest_count" id="cod_earning">$0.00</h4>
                                            <p class="mb-0 small text-dark-2">{{ trans('lang.cash_in_hand') }}</p>
                                        </div>
                                        <span class="box-icon ab"><img src="{{ asset('images/total_earning.png') }}"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card card-box-with-icon bg--3">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <div class="card-box-with-content">
                                            <h4 class="text-dark-2 mb-1 h4 total_transaction" id="total_withdrawal">$0.00</h4>
                                            <p class="mb-0 small text-dark-2">{{ trans('lang.total_withdrawal') }}</p>
                                        </div>
                                        <span class="box-icon ab"><img src="{{ asset('images/total_payment.png') }}"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card card-box-with-icon bg--5">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <div class="card-box-with-content">
                                            <h4 class="text-dark-2 mb-1 h4 commission_earned" id="pending_withdrawal">$0.00</h4>
                                            <p class="mb-0 small text-dark-2">{{ trans('lang.pending_withdrawal') }}</p>
                                        </div>
                                        <span class="box-icon ab"><img src="{{ asset('images/remaining_payment.png') }}"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row restaurant_payout_create driver_details">
                            <div class="restaurant_payout_create-inner">
                                <a href="javascript:void(0)" data-toggle="modal" data-target="#addWalletModal" class="add-wallate btn btn-success"><i class="fa fa-plus"></i> Add Wallet Amount</a>
                                <fieldset>
                                    <legend>{{ trans('lang.driver_details') }}</legend>
                                    <div class="form-group row width-50">
                                        <label class="col-3 control-label">{{ trans('lang.first_name') }}</label>
                                        <div class="col-7" class="driver_name">
                                            <span class="driver_name" id="driver_name"></span>
                                        </div>
                                    </div>
                                    <div class="form-group row width-50">
                                        <label class="col-3 control-label">{{ trans('lang.email') }}</label>
                                        <div class="col-7">
                                            <span class="email"></span>
                                        </div>
                                    </div>
                                    <div class="form-group row width-50">
                                        <label class="col-3 control-label">{{ trans('lang.user_phone') }}</label>
                                        <div class="col-7">
                                            <span class="phone"></span>
                                        </div>
                                    </div>
                                    <div class="form-group row width-50">
                                        <label class="col-3 control-label">{{ trans('lang.wallet_Balance') }}</label>
                                        <div class="col-7">
                                            <span class="wallet"></span>
                                        </div>
                                    </div>
                                    <div class="form-group row width-50">
                                        <label class="col-3 control-label">{{ trans('lang.profile_image') }}</label>
                                        <div class="col-7 profile_image">
                                        </div>
                                    </div>
                                    <div class="form-group row width-50">
                                        <label class="col-3 control-label">{{ trans('lang.zone') }}</label>
                                        <div class="col-7">
                                            <span id="zone_name"></span>
                                        </div>
                                    </div>
                            </div>
                            </fieldset>
                        </div>
                    </div>
                    <div class="row restaurant_payout_create restaurant_details">
                        <div class="restaurant_payout_create-inner">
                            <fieldset>
                                <legend>{{ trans('lang.bankdetails') }}</legend>
                                <div class="form-group row width-50">
                                    <label class="col-4 control-label">{{ trans('lang.bank_name') }}</label>
                                    <div class="col-7">
                                        <span class="bank_name"></span>
                                    </div>
                                </div>
                                <div class="form-group row width-50">
                                    <label class="col-4 control-label">{{ trans('lang.branch_name') }}</label>
                                    <div class="col-7">
                                        <span class="branch_name"></span>
                                    </div>
                                </div>
                                <div class="form-group row width-50">
                                    <label class="col-4 control-label">{{ trans('lang.holer_name') }}</label>
                                    <div class="col-7">
                                        <span class="holer_name"></span>
                                    </div>
                                </div>
                                <div class="form-group row width-50">
                                    <label class="col-4 control-label">{{ trans('lang.account_number') }}</label>
                                    <div class="col-7">
                                        <span class="account_number"></span>
                                    </div>
                                </div>
                                <div class="form-group row width-50">
                                    <label class="col-4 control-label">{{ trans('lang.other_information') }}</label>
                                    <div class="col-7">
                                        <span class="other_information"></span>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group col-12 text-center btm-btn">
                <a href="{!! route('drivers') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{ trans('lang.cancel') }}</a>
            </div>
        </div>
    </div>
    </div>
    <div class="modal fade" id="addWalletModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered location_modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title locationModalTitle">{{ trans('lang.add_wallet_amount') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="">
                        <div class="form-row">
                            <div class="form-group row">
                                <div class="form-group row width-100">
                                    <label class="col-12 control-label">{{ trans('lang.amount') }}</label>
                                    <div class="col-12">
                                        <input type="number" name="amount" class="form-control" id="amount">
                                        <div id="wallet_error" style="color:red"></div>
                                    </div>
                                </div>
                                <div class="form-group row width-100">
                                    <label class="col-12 control-label">{{ trans('lang.note') }}</label>
                                    <div class="col-12">
                                        <input type="text" name="note" class="form-control" id="note">
                                    </div>
                                </div>
                                <div class="form-group row width-100">
                                    <div id="user_account_not_found_error" class="align-items-center" style="color:red"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary save-form-btn">{{ trans('submit') }}</a>
                        </button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                            {{ trans('close') }}</a>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        var id = "<?php echo $id; ?>";
        var database = firebase.firestore();
        var ref = database.collection('users').where("id", "==", id);
        var photo = "";
        var placeholderImage = '';
        var placeholder = database.collection('settings').doc('placeHolderImage');
        placeholder.get().then(async function(snapshotsimage) {
            var placeholderImageData = snapshotsimage.data();
            placeholderImage = placeholderImageData.image;
        })
        var currentCurrency = '';
        var currencyAtRight = false;
        var decimal_degits = 0;
        var refCurrency = database.collection('currencies').where('isActive', '==', true);
        refCurrency.get().then(async function(snapshots) {
            var currencyData = snapshots.docs[0].data();
            currentCurrency = currencyData.symbol;
            currencyAtRight = currencyData.symbolAtRight;
            if (currencyData.decimal_degits) {
                decimal_degits = currencyData.decimal_degits;
            }
        });
        var email_templates = database.collection('email_templates').where('type', '==', 'wallet_topup');
        var emailTemplatesData = null;
        $(document).ready(async function() {
            jQuery("#data-table_processing").show();
            await email_templates.get().then(async function(snapshots) {
                emailTemplatesData = snapshots.docs[0].data();
            });
            getOverviewBlocksData();
            ref.get().then(async function(snapshots) {
                var driver = snapshots.docs[0].data();
                $(".driver_name").text(driver.firstName);
                if (driver.hasOwnProperty('email') && driver.email) {
                    $(".email").text(shortEmail(driver.email));
                } else {
                    $('.email').html("{{ trans('lang.not_mentioned') }}");
                }
                if (driver.hasOwnProperty('phoneNumber') && driver.phoneNumber) {
                    $(".phone").text(shortEditNumber(driver.phoneNumber));
                } else {
                    $('.phone').html("{{ trans('lang.not_mentioned') }}");
                }
                var wallet_balance = 0;
                if (driver.hasOwnProperty('wallet_amount') && driver.wallet_amount != null && !isNaN(driver.wallet_amount)) {
                    wallet_balance = driver.wallet_amount;
                }
                if (currencyAtRight) {
                    wallet_balance = parseFloat(wallet_balance).toFixed(decimal_degits) + "" + currentCurrency;
                } else {
                    wallet_balance = currentCurrency + "" + parseFloat(wallet_balance).toFixed(decimal_degits);
                }
                $(".wallet").text(wallet_balance);
                var image = "";
                if (driver.profilePictureURL != "" && driver.profilePictureURL != null) {
                    image = '<img onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" width="200px" id="" height="auto" src="' + driver.profilePictureURL + '">';
                } else {
                    image = '<img width="200px" id="" height="auto" src="' + placeholderImage + '">';
                }
                $(".profile_image").html(image);
                if (driver.hasOwnProperty('zoneId') && driver.zoneId != '') {
                    database.collection('zone').doc(driver.zoneId).get().then(async function(snapshots) {
                        let zone = snapshots.data();
                        $("#zone_name").text(zone.name);
                    });
                }
                if (driver.userBankDetails) {
                    if (driver.userBankDetails.bankName != undefined) {
                        $(".bank_name").text(driver.userBankDetails.bankName);
                    }
                    if (driver.userBankDetails.branchName != undefined) {
                        $(".branch_name").text(driver.userBankDetails.branchName);
                    }
                    if (driver.userBankDetails.holderName != undefined) {
                        $(".holer_name").text(driver.userBankDetails.holderName);
                    }
                    if (driver.userBankDetails.accountNumber != undefined) {
                        $(".account_number").text(driver.userBankDetails.accountNumber);
                    }
                    if (driver.userBankDetails.otherDetails != undefined) {
                        $(".other_information").text(driver.userBankDetails.otherDetails);
                    }
                }
                jQuery("#data-table_processing").hide();
            });
        })
        $(".save-form-btn").click(function() {
            var date = firebase.firestore.FieldValue.serverTimestamp();
            var amount = $('#amount').val();
            if (amount == '') {
                $('#wallet_error').text('{{ trans('lang.add_wallet_amount_error') }}');
                return false;
            }
            var note = $('#note').val();
            database.collection('users').where('id', '==', id).get().then(async function(snapshot) {
                if (snapshot.docs.length > 0) {
                    var data = snapshot.docs[0].data();
                    var walletAmount = 0;
                    if (data.hasOwnProperty('wallet_amount') && !isNaN(data.wallet_amount) && data.wallet_amount != null) {
                        walletAmount = data.wallet_amount;
                    }
                    var user_id = data.id;
                    var newWalletAmount = parseFloat(walletAmount) + parseFloat(amount);
                    database.collection('users').doc(id).update({
                        'wallet_amount': newWalletAmount
                    }).then(function(result) {
                        var tempId = database.collection("tmp").doc().id;
                        database.collection('wallet').doc(tempId).set({
                            'amount': parseFloat(amount),
                            'date': date,
                            'isTopUp': true,
                            'id': tempId,
                            'order_id': '',
                            'payment_method': 'Wallet',
                            'payment_status': 'success',
                            'user_id': user_id,
                            'note': note,
                            'transactionUser': "driver",
                        }).then(async function(result) {
                            if (currencyAtRight) {
                                amount = parseInt(amount).toFixed(decimal_degits) + "" + currentCurrency;
                                newWalletAmount = newWalletAmount.toFixed(decimal_degits) + "" + currentCurrency;
                            } else {
                                amount = currentCurrency + "" + parseInt(amount).toFixed(decimal_degits);
                                newWalletAmount = currentCurrency + "" + newWalletAmount.toFixed(decimal_degits);
                            }
                            var formattedDate = new Date();
                            var month = formattedDate.getMonth() + 1;
                            var day = formattedDate.getDate();
                            var year = formattedDate.getFullYear();
                            month = month < 10 ? '0' + month : month;
                            day = day < 10 ? '0' + day : day;
                            formattedDate = day + '-' + month + '-' + year;
                            var message = emailTemplatesData.message;
                            message = message.replace(/{username}/g, data.firstName + ' ' + data.lastName);
                            message = message.replace(/{date}/g, formattedDate);
                            message = message.replace(/{amount}/g, amount);
                            message = message.replace(/{paymentmethod}/g, 'Wallet');
                            message = message.replace(/{transactionid}/g, tempId);
                            message = message.replace(/{newwalletbalance}/g, newWalletAmount);
                            emailTemplatesData.message = message;
                            var url = "{{ url('send-email') }}";
                            var sendEmailStatus = await sendEmail(url, emailTemplatesData.subject, emailTemplatesData.message, [data.email]);
                            if (sendEmailStatus) {
                                window.location.reload();
                            }
                        })
                    })
                } else {
                    $('#user_account_not_found_error').text('{{ trans('lang.user_detail_not_found') }}');
                }
            });
        });

        async function getOverviewBlocksData() {
            var totalCodEarning = 0;
            var driverWithrawal = 0;
            var pendingWithdrawal = 0;
            await database.collection('restaurant_orders').where('driverID', '==', id).where('payment_method', '==', 'cod').where('status', '==', 'Order Completed').get().then(async function(snapshot) {
                if (snapshot.docs.length > 0) {
                    console.log(snapshot.docs.length)
                    snapshot.docs.forEach((listval) => {
                        var data = listval.data();
                        if (data.hasOwnProperty('tip_amount') && data.tip_amount != null && data.tip_amount != '') {
                            totalCodEarning += parseFloat(data.tip_amount);
                        }
                        if (data.hasOwnProperty('deliveryCharge') && data.deliveryCharge != null && data.deliveryCharge != '') {
                            totalCodEarning += parseFloat(data.deliveryCharge);
                        }
                    })
                }
            })
           await database.collection('driver_payouts').where('driverID', '==', id).where('paymentStatus', '==', 'Success').get().then(async function(snapshot) {
                if (snapshot.docs.length > 0) {

                    snapshot.docs.forEach((listval) => {
                        var data = listval.data();
                        if (data.hasOwnProperty('amount') && data.amount != null && data.amount != '') {
                            driverWithrawal += parseFloat(data.amount);
                        }

                    })
                }
            })

           await database.collection('users').where('id', '==', id).get().then(async function(snapshot) {
                if (snapshot.docs.length > 0) {
                    var data = snapshot.docs[0].data();
                    if (data.hasOwnProperty('wallet_amount') && data.wallet_amount != null && !isNaN(data.wallet_amount)) {
                        pendingWithdrawal = data.wallet_amount;
                    }

                }
            })
            var totalEarning=parseFloat(pendingWithdrawal)+parseFloat(driverWithrawal)+parseFloat(totalCodEarning);
            if (currencyAtRight) {
                totalCodEarningAmount=parseFloat(totalCodEarning).toFixed(decimal_degits) + "" + currentCurrency;
                totalWithdrawalAmount = parseFloat(driverWithrawal).toFixed(decimal_degits) + "" + currentCurrency;
                pendingWithdrawalAmount = parseFloat(pendingWithdrawal).toFixed(decimal_degits) + "" + currentCurrency;
                totalEarningAmount=parseFloat(totalEarning).toFixed(decimal_degits) + "" + currentCurrency;
            } else {
                totalCodEarningAmount=currentCurrency + "" + parseFloat(totalCodEarning).toFixed(decimal_degits);
                totalWithdrawalAmount = currentCurrency + "" + parseFloat(driverWithrawal).toFixed(decimal_degits);
                pendingWithdrawalAmount = currentCurrency + "" + parseFloat(pendingWithdrawal).toFixed(decimal_degits);
                totalEarningAmount=currentCurrency + "" + parseFloat(totalEarning).toFixed(decimal_degits);
            }
            $('#cod_earning').html(totalCodEarningAmount);
            $('#total_withdrawal').html(totalWithdrawalAmount);
            $('#pending_withdrawal').html(pendingWithdrawalAmount)
            $('#total_earnings').html(totalEarningAmount);
        }
    </script>
@endsection
