@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.menu_items')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('setting.banners') !!}">{{trans('lang.menu_items')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.menu_items_create')}}</li>
            </ol>
        </div>
    </div>
    <div class="card-body">
        <div class="error_top"></div>
        <div class="row restaurant_payout_create">
            <div class="restaurant_payout_create-inner">
                <fieldset>
                    <legend>{{trans('lang.menu_items')}}</legend>
                    <div class="form-group row width-50">
                        <label class="col-3 control-label">{{trans('lang.title')}}</label>
                        <div class="col-7">
                            <input type="text" class="form-control title">
                        </div>
                    </div>
                    <div class="form-group row width-50">
                        <label class="col-3 control-label">{{trans('lang.set_order')}}</label>
                        <div class="col-7">
                            <input type="number" class="form-control set_order" min="0">
                        </div>
                    </div>
                    <div class="form-group row width-100">
                        <div class="form-check width-100">
                            <input type="checkbox" id="is_publish">
                            <label class="col-3 control-label" for="is_publish">{{trans('lang.is_publish')}}</label>
                        </div>
                    </div>
                    <div class="form-group row width-50">
                        <label class="col-3 control-label">{{trans('lang.photo')}}</label>
                        <input type="file" onChange="handleFileSelect(event)" class="col-7">
                        <div id="uploding_image"></div>
                        <div class="placeholder_img_thumb user_image"></div>
                    </div>
                    <div class="form-group row width-50" id="banner_position">
                        <label class="col-3 control-label ">{{trans('lang.banner_position')}}</label>
                        <div class="col-7">
                            <select name="position" id="position" class="form-control">
                                <option value="top">{{trans('lang.top')}}</option>
                                <option value="middle">{{trans('lang.middle')}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row width-100 radio-form-row d-flex" id="redirect_type_div">
                        <div class="radio-form col-md-2">
                            <input type="radio" class="redirect_type" value="store" name="redirect_type" id="store">
                            <label class="custom-control-label">{{trans('lang.store')}}</label>
                        </div>
                        <div class="radio-form col-md-2">
                            <input type="radio" class="redirect_type" value="product" name="redirect_type" id="product">
                            <label class="custom-control-label">{{trans('lang.product')}}</label>
                        </div>
                        <div class="radio-form col-md-4">
                            <input type="radio" class="redirect_type" value="external_link" name="redirect_type" id="external">
                            <label class="custom-control-label">{{trans('lang.external_link')}}</label>
                        </div>
                    </div>
                    <div class="form-group row width-50" id="vendor_div" style="display: none;">
                        <label class="col-3 control-label ">{{trans('lang.store')}}</label>
                        <div class="col-7">
                            <select name="storeId" id="storeId" class="form-control">
                            </select>
                        </div>
                    </div>
                    <div class="form-group row width-50" id="product_div" style="display: none;">
                        <label class="col-3 control-label ">{{trans('lang.product')}}</label>
                        <div class="col-7">
                            <select name="productId" id="productId" class="form-control">
                            </select>
                        </div>
                    </div>
                    <div class="form-group row width-100" id="external_link_div" style="display: none;">
                        <label class="col-3 control-label">{{trans('lang.external_link')}}</label>
                        <div class="col-7">
                            <input type="text" class="form-control" id="external_link">
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
    <div class="form-group col-12 text-center">
        <button type="button" class="btn btn-primary  save-setting-btn"><i class="fa fa-save"></i> {{trans('lang.save')}}</button>
        <a href="{!! route('setting.banners') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{trans('lang.cancel')}}</a>
    </div>
</div>
@endsection
@section('scripts')
<script>
    var database = firebase.firestore();
    var photo = "";
    var fileName = '';
    var storageRef = firebase.storage().ref('images');
    $("input[name='redirect_type']:radio").change(function() {
        var redirect_type = $(this).val();
        if (redirect_type == "store") {
            getTypeWiseDetails('store');
            $('#vendor_div').show();
            $('#product_div').hide();
            $('#external_link_div').hide();
        } else if (redirect_type == "product") {
            getTypeWiseDetails('product');
            $('#vendor_div').hide();
            $('#product_div').show();
            $('#external_link_div').hide();
        } else if (redirect_type == "external_link") {
            $('#vendor_div').hide();
            $('#product_div').hide();
            $('#external_link_div').show();
        }
    });
    function handleFileSelect(evt) {
        var f = evt.target.files[0];
        var reader = new FileReader();
        reader.onload = (function(theFile) {
            return function(e) {
                var filePayload = e.target.result;
                var val = f.name;
                var ext = val.split('.')[1];
                var docName = val.split('fakepath')[1];
                var filename = (f.name).replace(/C:\\fakepath\\/i, '')
                photo = filePayload;
				fileName = filename;
                $(".user_image").empty();
                $(".user_image").append('<img class="rounded" style="width:50px" src="' + photo + '" alt="image">');
                $("#banner_img").val('');
            };
        })(f);
        reader.readAsDataURL(f);
    }
    async function storeImageData() {
        var newPhoto = '';
        try {
            photo = photo.replace(/^data:image\/[a-z]+;base64,/, "")
            var uploadTask = await storageRef.child(fileName).putString(photo, 'base64', {
                contentType: 'image/jpg'
            });
            var downloadURL = await uploadTask.ref.getDownloadURL();
            newPhoto = downloadURL;
            photo = downloadURL;
        } catch (error) {
            console.log("ERR ===", error);
        }
        return newPhoto;
    }
    $(".save-setting-btn").click(function() {
    var title = $(".title").val();
    var position = $("#position").val();
    var set_order = parseInt($('.set_order').val());
    var banner_img = $("#banner_img").val();
    var is_publish = false;
    var redirect_type = "";
    if ($(".redirect_type").is(":visible")) {
        redirect_type = $(".redirect_type:checked").val() || "";
    }
    var redirect_id = "";
    var checkFlag = true;
    var checkFlagRedirection = true;
    if (redirect_type == "store") {
        redirect_id = $('#storeId').val();
        if (redirect_id == "") {
            checkFlag = false;
            checkFlagRedirection = "store";
        }
    } else if (redirect_type == "product") {
        redirect_id = $('#productId').val();
        if (redirect_id == "") {
            checkFlag = false;
            checkFlagRedirection = "product";
        }
    } else if (redirect_type == "external_link") {
        redirect_id = $('#external_link').val();
        if (redirect_id == "") {
            checkFlag = false;
            checkFlagRedirection = "external_link";
        }
    }
    if ($("#is_publish").is(':checked')) {
        is_publish = true;
    }
        var storeId = $("#storeId").val();
        var productId = $('#productId').val();
        var ext = $("#external_link").val();
        var store_rd = $("#store").is(':checked');
        var product_rd = $("#product").is(':checked');
        var ext_rd = $("#external").is(':checked');
    if (title == '') {
        $(".error_top").show();
        $(".error_top").html("");
        $(".error_top").append("<p>{{trans('lang.title_error')}}</p>");
        window.scrollTo(0, 0);
    } else if (isNaN(set_order)) {
        $(".error_top").show();
        $(".error_top").html("");
        $(".error_top").append("<p>{{trans('lang.set_order_error')}}</p>");
        window.scrollTo(0, 0);
    }
    else if(storeId == '' && store_rd == true) {
        $(".error_top").show();
        $(".error_top").html("");
        $(".error_top").append("<p>{{trans('lang.set_store_error')}}</p>");
        window.scrollTo(0, 0);
    } else if(productId == '' && product_rd == true) {
        $(".error_top").show();
        $(".error_top").html("");
        $(".error_top").append("<p>{{trans('lang.set_product_error')}}</p>");
        window.scrollTo(0, 0);
    } else if(ext == '' && ext_rd == true) {
        $(".error_top").show();
        $(".error_top").html("");
        $(".error_top").append("<p>{{trans('lang.set_external_error')}}</p>");
        window.scrollTo(0, 0);
    }
    else if(photo == ''){
        $(".error_top").show();
        $(".error_top").html("");
        $(".error_top").append("<p>{{trans('lang.please_choose_banner')}}</p>");
        window.scrollTo(0, 0);
    }
     else if (redirect_type == '') {
        $(".error_top").show();
        $(".error_top").html("");
        if (checkFlagRedirection == "external_link") {
            $(".error_top").append("<p>{{trans('lang.external_redirection')}}</p>");
        } else {
            $(".error_top").append("<p>{{trans('lang.redirect_type')}}</p>");
        }
        window.scrollTo(0, 0);
    } else {
        var id = "<?php echo uniqid(); ?>";
        storeImageData().then(IMG => {
            database.collection('menu_items').doc(id).set({
                'title': title,
                'photo': IMG,
                'id': id,
                'set_order': set_order,
                'is_publish': is_publish,
                'position': position,
                'redirect_type': redirect_type,
                'redirect_id': redirect_id
            }).then(function(result) {
                window.location.href = '{{ route("setting.banners")}}';
            }).catch(function(error) {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>" + error + "</p>");
            });
        }).catch(function(error) {
            jQuery("#data-table_processing").hide();
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>" + error + "</p>");
        });
    }
});
    function getTypeWiseDetails(redirect_type, sectionId) {
        if (redirect_type == "store") {
            $('#storeId').html("");
            $('#storeId').append($("<option value=''>Select Restaurant</option>"));
            var ref_vendors = database.collection('vendors');
            ref_vendors.get().then(async function(snapshots) {
                snapshots.docs.forEach((listval) => {
                    var data = listval.data();
                    $('#storeId').append($("<option></option>")
                        .attr("value", data.id)
                        .text(data.title));
                })
            })
        } else if (redirect_type == "product") {
            $('#productId').html("");
            $('#productId').append($("<option value=''>Select Food</option>"));
            var ref_vendor_products = database.collection('vendor_products');
            ref_vendor_products.get().then(async function(snapshots) {
                snapshots.docs.forEach((listval) => {
                    var data = listval.data();
                    $('#productId').append($("<option></option>")
                        .attr("value", data.id)
                        .text(data.name));
                })
            })
        }
    }
</script>
@endsection
