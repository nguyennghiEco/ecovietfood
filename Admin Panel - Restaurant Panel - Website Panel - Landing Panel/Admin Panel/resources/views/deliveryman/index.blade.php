@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{ trans('lang.deliveryman') }}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                    <li class="breadcrumb-item active">{{ trans('lang.deliveryman') }}</li>
                </ol>
            </div>
            <div>
            </div>
        </div>
        <div class="row px-5 mb-2">
            <div class="col-12">
                <span class="font-weight-bold text-danger food-limit-note"></span>
            </div>
        </div>
        <div class="container-fluid">
            <div class="admin-top-section">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex top-title-section pb-4 justify-content-between">
                            <div class="d-flex top-title-left align-self-center">
                                <span class="icon mr-3"><img src="{{ asset('images/category.png') }}"></span>
                                <h3 class="mb-0">{{ trans('lang.deliveryman_table') }}</h3>
                                <span class="counter ml-3 total_count"></span>
                            </div>
                            <div class="d-flex top-title-right align-self-center">
                                <div class="select-box pl-3">
                                    <select class="form-control status_selector filteredRecords">
                                        <option value="" selected>{{ trans('lang.status') }}</option>
                                        <option value="active">{{ trans('lang.active') }}</option>
                                        <option value="inactive">{{ trans('lang.in_active') }}</option>
                                    </select>
                                </div>
                                <div class="select-box pl-3">
                                    <div id="daterange"><i class="fa fa-calendar"></i>&nbsp;
                                        <span></span>&nbsp; <i class="fa fa-caret-down"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="table-list">
                <div class="row">
                    <div class="col-12">
                        <?php if ($id != '') { ?>
                        <div class="menu-tab">
                            <ul>
                                <li>
                                    <a href="{{ route('restaurants.view', $id) }}">{{ trans('lang.tab_basic') }}</a>
                                </li>
                                <li>
                                    <a href="{{ route('restaurants.foods', $id) }}">{{ trans('lang.tab_foods') }}</a>
                                </li>
                                <li>
                                    <a href="{{ route('restaurants.orders', $id) }}">{{ trans('lang.tab_orders') }}</a>
                                </li>
                                <li>
                                    <a href="{{ route('restaurants.coupons', $id) }}">{{ trans('lang.tab_promos') }}</a>
                                <li>
                                    <a href="{{ route('restaurants.payout', $id) }}">{{ trans('lang.tab_payouts') }}</a>
                                </li>
                                <li>
                                    <a href="{{ route('payoutRequests.restaurants.view', $id) }}">{{ trans('lang.tab_payout_request') }}</a>
                                </li>
                                <li>
                                    <a href="{{ route('restaurants.booktable', $id) }}">{{ trans('lang.dine_in_future') }}</a>
                                </li>
                                <li id="restaurant_wallet"></li>
                                <li id="subscription_plan"></li>
                                <li>
                                    <a href="{{ route('restaurants.advertisements', $id) }}">{{ trans('lang.advertisement_plural') }}</a>
                                </li>
                                <li class="active">
                                    <a href="{{ route('restaurants.deliveryman', $id) }}">{{ trans('lang.deliveryman') }}</a>
                                </li>
                            </ul>
                        </div>
                        <?php } ?>

                        <div class="card border">
                            <div class="card-header d-flex justify-content-between align-items-center border-0">
                                <div class="card-header-title">
                                    <h3 class="text-dark-2 mb-2 h4">{{ trans('lang.deliveryman_table') }}</h3>
                                    <p class="mb-0 text-dark-2">{{ trans('lang.deliveryman_table_text') }}</p>
                                </div>
                                <div class="card-header-right d-flex align-items-center">
                                    <div class="card-header-btn mr-3">
                                        <a class="btn-primary btn rounded-full" href="{!! route('deliveryman.create') !!}"><i class="mdi mdi-plus mr-2"></i>{{ trans('lang.create_deliveryman') }}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive m-t-10">
                                    <table id="deliverymanTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">
                                        <thead>
                                            <?php if (in_array('deliveryman.delete', json_decode(@session('user_permissions'),true))) {
                                            ?>
                                            <th class="delete-all"><input type="checkbox" id="is_active"><label class="col-3 control-label" for="is_active">
                                                    <a id="deleteAll" class="do_not_delete" href="javascript:void(0)"><i class="mdi mdi-delete"></i> {{ trans('lang.all') }}</a></label></th>
                                            <?php }
                                            ?>
                                            <th>{{ trans('lang.user_info') }}</th>
                                            <th>{{ trans('lang.email') }}</th>
                                            <?php if ($id == '') { ?>
                                            <th>{{ trans('lang.res_info') }}</th>
                                            <?php } ?>
                                            <th>{{ trans('lang.active') }}</th>
                                            <th>{{ trans('lang.date') }}</th>
                                            <th>{{ trans('lang.dashboard_total_orders') }}</th>
                                            <th>{{ trans('lang.actions') }}</th>
                                        </thead>
                                        <tbody id="append_list1">
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
    </div>
    </div>
@endsection
@section('scripts')
    <script type="text/javascript">
        var database = firebase.firestore();
        var id = "{{ $id }}";
        if (id != '') {
            database.collection('vendors').where("id", "==", '{{ $id }}').get().then(async function(snapshots) {
                var vendorData = snapshots.docs[0].data();
                walletRoute = "{{ route('users.walletstransaction', ':id') }}";
                walletRoute = walletRoute.replace(":id", vendorData.author);
                $('#restaurant_wallet').append('<a href="' + walletRoute + '">{{ trans('lang.wallet_transaction') }}</a>');
                $('#subscription_plan').append('<a href="' + "{{ route('vendor.subscriptionPlanHistory', ':id') }}".replace(':id', vendorData.author) + '">' + '{{ trans('lang.subscription_history') }}' + '</a>');
            });
            ref = database.collection('users').where("role", "==", "driver").where("vendorID", "==", id);
        } else {
            ref = database.collection('users').where("role", "==", "driver").where("vendorID", "!=", null).orderBy('vendorID');
        }
        var offest = 1;
        var pagesize = 10;
        var end = null;
        var endarray = [];
        var start = null;

        var ref;
        var append_list = '';
        var placeholderImage = '';
        var user_permissions = '<?php echo @session('user_permissions'); ?>';
        user_permissions = Object.values(JSON.parse(user_permissions));
        var placeholderImage = '';
        var checkDeletePermission = false;
        if ($.inArray('deliveryman.delete', user_permissions) >= 0) {
            checkDeletePermission = true;
        }
        var placeholder = database.collection('settings').doc('placeHolderImage');
        placeholder.get().then(async function(snapshotsimage) {
            var placeholderImageData = snapshotsimage.data();
            placeholderImage = placeholderImageData.image;
        });
        $('.status_selector').select2({
            placeholder: "{{ trans('lang.select_status') }}",
            minimumResultsForSearch: Infinity,
            allowClear: true
        });
        $('select').on("select2:unselecting", function(e) {
            var self = $(this);
            setTimeout(function() {
                self.select2('close');
            }, 0);
        });

        function setDate() {
            $('#daterange span').html('{{ trans('lang.select_range') }}');
            $('#daterange').daterangepicker({
                autoUpdateInput: false,
            }, function(start, end) {
                $('#daterange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                $('.filteredRecords').trigger('change');
            });
            $('#daterange').on('apply.daterangepicker', function(ev, picker) {
                $('#daterange span').html(picker.startDate.format('MMMM D, YYYY') + ' - ' + picker.endDate.format('MMMM D, YYYY'));
                $('.filteredRecords').trigger('change');
            });
            $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
                $('#daterange span').html('{{ trans('lang.select_range') }}');
                $('.filteredRecords').trigger('change');
            });
        }
        setDate();
        var initialRef = ref;
        $('.filteredRecords').change(async function() {
            var status = $('.status_selector').val();
            var daterangepicker = $('#daterange').data('daterangepicker');
            var refData = initialRef;
            if (status) {
                refData = (status === "active") ?
                    refData.where('active', '==', true) :
                    refData.where('active', '==', false);
            }
            if ($('#daterange span').html() != '{{ trans('lang.select_range') }}' && daterangepicker) {
                var from = moment(daterangepicker.startDate).toDate();
                var to = moment(daterangepicker.endDate).toDate();
                if (from && to) {
                    var fromDate = firebase.firestore.Timestamp.fromDate(new Date(from));
                    refData = refData.where('createdAt', '>=', fromDate);
                    var toDate = firebase.firestore.Timestamp.fromDate(new Date(to));
                    refData = refData.where('createdAt', '<=', toDate);
                }
            }
            ref = refData;
            $('#deliverymanTable').DataTable().ajax.reload();
        });

        $(document).ready(function() {

            $(document.body).on('click', '.redirecttopage', function() {
                var url = $(this).attr('data-url');
                window.location.href = url;
            });
            var placeholder = database.collection('settings').doc('placeHolderImage');
            placeholder.get().then(async function(snapshotsimage) {
                var placeholderImageData = snapshotsimage.data();
                placeholderImage = placeholderImageData.image;
            })
            $(document).on('click', '.dt-button-collection .dt-button', function() {
                $('.dt-button-collection').hide();
                $('.dt-button-background').hide();
            });
            $(document).on('click', function(event) {
                if (!$(event.target).closest('.dt-button-collection, .dt-buttons').length) {
                    $('.dt-button-collection').hide();
                    $('.dt-button-background').hide();
                }
            });
            @if ($id != '')
                var fieldConfig = {
                    columns: [{
                            key: 'driverName',
                            header: "{{ trans('lang.user_name') }}"
                        },
                        {
                            key: 'email',
                            header: "{{ trans('lang.email') }}"
                        },

                        {
                            key: 'active',
                            header: "{{ trans('lang.driver_active') }}"
                        },
                        {
                            key: 'createdAt',
                            header: "{{ trans('lang.created_at') }}"
                        },
                    ],
                    fileName: "{{ trans('lang.deliveryman_table') }}",
                };
            @else
                var fieldConfig = {
                    columns: [{
                            key: 'driverName',
                            header: "{{ trans('lang.user_name') }}"
                        },
                        {
                            key: 'email',
                            header: "{{ trans('lang.email') }}"
                        },
                        {
                            key: 'vendorTitle',
                            header: "{{ trans('lang.restaurant') }}"
                        },
                        {
                            key: 'active',
                            header: "{{ trans('lang.driver_active') }}"
                        },
                        {
                            key: 'createdAt',
                            header: "{{ trans('lang.created_at') }}"
                        },
                    ],
                    fileName: "{{ trans('lang.deliveryman_table') }}",
                };
            @endif

            const table = $('#deliverymanTable').DataTable({
                pageLength: 10, // Number of rows per page
                processing: false,
                serverSide: true,
                responsive: true,
                ajax: async function(data, callback, settings) {
                    const start = data.start;
                    const length = data.length;
                    const searchValue = data.search.value.toLowerCase();
                    const orderColumnIndex = data.order[0].column;
                    const orderDirection = data.order[0].dir;
                    @if ($id != '')
                        const orderableColumns = (checkDeletePermission) ? ['', 'driverName', 'email', '', 'createdAt', '', ''] : ['driverName', 'email', '', 'createdAt', '', ''];
                    @else
                        const orderableColumns = (checkDeletePermission) ? ['', 'driverName', 'email', 'vendorTitle', '', 'createdAt', '', ''] : ['driverName', 'email', 'vendorTitle', '', 'createdAt', '', ''];
                    @endif
                    const orderByField = orderableColumns[orderColumnIndex];

                    if (searchValue.length >= 3 || searchValue.length === 0) {
                        $('#data-table_processing').show();
                    }
                    try {

                        const querySnapshot = await ref.orderBy('createdAt', 'desc').get();
                        if (!querySnapshot || querySnapshot.empty) {
                            $('.total_count').text(0);
                            console.error("No data found in Firestore.");
                            $('#data-table_processing').hide(); // Hide loader
                            callback({
                                draw: data.draw,
                                recordsTotal: 0,
                                recordsFiltered: 0,
                                 filteredData: [],
                                data: [] // No data
                            });
                            return;
                        }
                        let records = [];
                        let filteredRecords = [];
                        await Promise.all(querySnapshot.docs.map(async (doc) => {
                            let childData = doc.data();
                            childData.id = doc.id;
                            childData.driverName = childData.firstName + ' ' + childData.lastName || " "
                            childData.vendorID = childData.vendorID || childData.vendorID;

                            if (childData.vendorID) {
                                let vendorDetail = await getRestaurant(childData.vendorID);
                                childData.vendorTitle = vendorDetail.title;
                                childData.vendorImage = vendorDetail.image;
                                childData.vendorEmail = vendorDetail.email;
                            } else {
                                childData.vendorTitle = "-";
                                childData.vendorImage = "-";
                                childData.vendorEmail = "-";
                            }
                            const options = {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            };
                            var date = '';
                            var time = '';
                            if (childData.hasOwnProperty("createdAt")) {
                                try {
                                    date = childData.createdAt.toDate().toDateString();
                                    time = childData.createdAt.toDate().toLocaleTimeString('en-US');
                                } catch (err) {}
                            }
                            childData.createdDate = date + ' ' + time;


                            if (searchValue) {
                                if (
                                    (childData.driverName && childData.driverName.toString().toLowerCase().includes(searchValue)) ||
                                    (childData.createdDate && childData.createdDate.toString().toLowerCase().indexOf(searchValue) > -1) ||
                                    (childData.email && childData.email.toString().includes(searchValue)) ||
                                    (childData.vendorTitle && childData.vendorTitle.toString().toLowerCase().includes(searchValue))


                                ) {
                                    filteredRecords.push(childData);
                                }
                            } else {
                                filteredRecords.push(childData);
                            }
                        }));
                        filteredRecords.sort((a, b) => {
                            let aValue = a[orderByField] ? a[orderByField].toString().toLowerCase() : '';
                            let bValue = b[orderByField] ? b[orderByField].toString().toLowerCase() : '';
                            if (orderByField === 'createdAt') {
                                aValue = a[orderByField] ? new Date(a[orderByField].toDate()).getTime() : 0;
                                bValue = b[orderByField] ? new Date(b[orderByField].toDate()).getTime() : 0;
                            }
                            if (orderDirection === 'asc') {
                                return (aValue > bValue) ? 1 : -1;
                            } else {
                                return (aValue < bValue) ? 1 : -1;
                            }
                        });
                        const totalRecords = filteredRecords.length;
                        $('.total_count').text(totalRecords);
                        const paginatedRecords = filteredRecords.slice(start, start + length);
                        await Promise.all(paginatedRecords.map(async (childData) => {
                            if (childData.id) {
                                const totalOrderSnapShot = await database.collection('restaurant_orders').where('driverID', '==', childData.id).get();
                                const orders = totalOrderSnapShot.size;
                                childData.orders = orders;
                            } else {
                                childData.orders = 0;
                            }
                        }));
                        const formattedRecords = await Promise.all(paginatedRecords.map(async (
                            childData) => {
                            return await buildHTML(childData);
                        }));
                        $('#data-table_processing').hide(); // Hide loader
                        callback({
                            draw: data.draw,
                            recordsTotal: totalRecords,
                            recordsFiltered: totalRecords,
                            filteredData: filteredRecords,
                            data: formattedRecords
                        });
                    } catch (error) {
                        console.error("Error fetching data from Firestore:", error);
                        jQuery('#overlay').hide();
                        callback({
                            draw: data.draw,
                            recordsTotal: 0,
                            recordsFiltered: 0,
                            filteredData: [],
                            data: []
                        });
                    }
                },


                order: (id != '') ? (checkDeletePermission) ? [4, 'desc'] : [3, 'desc'] : (checkDeletePermission) ? [5, 'desc'] : [4, 'desc'],
                columnDefs: [{
                    orderable: false,
                    targets: (id != '') ? (checkDeletePermission) ? [0, 3, 5, 6] : [2, 4, 5] : (checkDeletePermission) ? [0, 4, 6, 7] : [3, 5, 6]
                }, ],
                "language": {
                    "zeroRecords": "{{ trans('lang.no_record_found') }}",
                    "emptyTable": "{{ trans('lang.no_record_found') }}",
                    "processing": ""
                },
                dom: 'lfrtipB',
                buttons: [{
                    extend: 'collection',
                    text: '<i class="mdi mdi-cloud-download"></i> Export as',
                    className: 'btn btn-info',
                    buttons: [{
                            extend: 'excelHtml5',
                            text: 'Export Excel',
                            action: function(e, dt, button, config) {
                                exportData(dt, 'excel', fieldConfig);
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            text: 'Export PDF',
                            action: function(e, dt, button, config) {
                                exportData(dt, 'pdf', fieldConfig);
                            }
                        },
                        {
                            extend: 'csvHtml5',
                            text: 'Export CSV',
                            action: function(e, dt, button, config) {
                                exportData(dt, 'csv', fieldConfig);
                            }
                        }
                    ]
                }],

                initComplete: function() {
                    $(".dataTables_filter").append($(".dt-buttons").detach());
                    $('.dataTables_filter input').attr('placeholder', 'Search here...').attr('autocomplete', 'new-password').val('');
                    $('.dataTables_filter label').contents().filter(function() {
                        return this.nodeType === 3;
                    }).remove();
                }
            });

            function debounce(func, wait) {
                let timeout;
                const context = this;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(context, args), wait);
                };
            }
            $('#search-input').on('input', debounce(function() {
                const searchValue = $(this).val();
                if (searchValue.length >= 3) {
                    $('#data-table_processing').show();
                    table.search(searchValue).draw();
                } else if (searchValue.length === 0) {
                    $('#data-table_processing').show();
                    table.search('').draw();
                }
            }, 300));

        });

        async function buildHTML(val) {
            var html = [];
            var id = val.id;
            var route1 = '{{ route('deliveryman.edit', ':id') }}';
            route1 = route1.replace(':id', id);
            if (checkDeletePermission) {
                html.push('<td class="delete-all"><input type="checkbox" id="is_open_' + id + '" class="is_open" dataId="' +
                    id + '"><label class="col-3 control-label"\n' +
                    'for="is_open_' + id + '" ></label></td>');
            }

            var driverImage = val.profilePictureURL == '' || val.profilePictureURL == null ? '<img alt="" width="100%" style="width:70px;height:70px;" src="' + placeholderImage + '" alt="image">' : '<img onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" alt="" width="100%" style="width:70px;height:70px;" src="' + val.profilePictureURL + '" alt="image">'

            html.push('<td>' + driverImage + '<a href="' + route1 + '">' + val.driverName + '</a></td>');
            html.push('<td>' + val.email + '</td>');
            if ("{{ $id }}" == '') {
                html.push('<td><img src="' + val.vendorImage + '" style="width:50px; height:50px; border-radius:50%;" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'">' + val.vendorTitle + '<br>' + val.vendorEmail + '</td>');
            }

            if (val.active) {
                html.push('<td><label class="switch"><input type="checkbox" checked id="' + val.id + '" name="isActive"><span class="slider round"></span></label></td>')
            } else {
                html.push('<td><label class="switch"><input type="checkbox"  id="' + val.id + '" name="isActive"><span class="slider round"></span></label></td>')
            }
            html.push('<td>' + val.createdDate + '</td>');
            html.push('<td>' + val.orders + '</td>');

            var action = '';
            action = action + '<?php if(in_array('deliveryman.edit', json_decode(@session('user_permissions'),true))){ ?><span class="action-btn"><a href="' + route1 + '"><i class="fa fa-edit"></i></a><?php } ?>';
            action = action + '<?php if(in_array('deliveryman.delete', json_decode(@session('user_permissions'),true))){ ?><a id="' + val.id + '" class="do_not_delete" name="deliveryman-delete" href="javascript:void(0)"><i class="fa fa-trash"></i></a><?php } ?>';
            action = action + '</span>';
            html.push(action);
            return html;
        }
        $(document).on("click", "input[name='isActive']", function(e) {
            var ischeck = $(this).is(':checked');
            var id = this.id;
            if (ischeck) {
                database.collection('users').doc(id).update({
                    'active': true
                }).then(function(result) {
                    jQuery("#data-table_processing").hide();
                });
            } else {
                database.collection('users').doc(id).update({
                    'active': false
                }).then(function(result) {
                    jQuery("#data-table_processing").hide();
                });
            }
        });
        $("#is_active").click(function() {
            $("#deliverymanTable .is_open").prop('checked', $(this).prop('checked'));
        });
        $("#deleteAll").click(function() {
            console.log('here')
            if ($('#deliverymanTable .is_open:checked').length) {
                if (confirm('Are You Sure want to Delete Selected Data ?')) {
                    jQuery("#data-table_processing").show();
                    $('#deliverymanTable .is_open:checked').each(async function() {
                        var dataId = $(this).attr('dataId');
                        await deleteDocumentWithImage('users', dataId, ['profilePictureURL']);
                        await deleteDriverData(dataId).then(function() {
                            window.location.reload();
                        });
                    });
                }
            } else {
                alert('Please Select Any One Record .');
            }
        });

        $(document).on("click", "a[name='deliveryman-delete']", async function(e) {
            var id = this.id;
            await deleteDocumentWithImage('users', dataId, ['profilePictureURL']);
            await deleteDriverData(id).then(function() {
                window.location.reload();
            });
        });
        async function deleteDriverData(driverId) {
            //delete user from authentication  
            var dataObject = {
                "data": {
                    "uid": driverId
                }
            };
            var projectId = '<?php echo env('FIREBASE_PROJECT_ID'); ?>';
            jQuery.ajax({
                url: 'https://us-central1-' + projectId + '.cloudfunctions.net/deleteUser',
                method: 'POST',
                contentType: "application/json; charset=utf-8",
                data: JSON.stringify(dataObject),
                success: function(data) {
                    console.log('Delete user success:', data.result);
                },
                error: function(xhr, status, error) {
                    var responseText = JSON.parse(xhr.responseText);
                    console.log('Delete user error:', responseText.error);
                }
            });
        }

        async function getRestaurant(vendorid) {

            const vendorRef = database.collection('vendors').where('id', '==', vendorid);
            const vendorSnapshot = await vendorRef.get();

            const vendor_userRef = database.collection('users').where('vendorID', '==', vendorid).where('role', '==', 'vendor');
            const vendor_userSnapshot = await vendor_userRef.get();


            if (vendorSnapshot.empty) {
                return {
                    title: "-"
                }; // Default values if vendor not found
            }

            if (vendor_userSnapshot.empty) {
                return {
                    image: "-",
                    email: "-"
                }; // Default values if vendor not found
            }

            let vendorData = {};
            let vendor_userData = {};
            vendorSnapshot.forEach((doc) => {
                vendorData = doc.data();
            });

            vendor_userSnapshot.forEach((doc) => {
                vendor_userData = doc.data();
            });

            return {
                title: vendorData.title || "-",
                image: vendorData.profilePictureURL == '' || vendorData.profilePictureURL == null ? placeholderImage : val.profilePictureURL,
                email: vendor_userData.email || "-"
            };
        }
    </script>
@endsection
