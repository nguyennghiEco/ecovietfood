@extends('layouts.app')
@section('content')
<div class="page-wrapper">
  <div class="row page-titles">
    <div class="col-md-5 align-self-center">
      <h3 class="text-themecolor">{{trans('lang.coupon_plural')}}</h3>
    </div>
    <div class="col-md-7 align-self-center">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
        <li class="breadcrumb-item"><a href="{!! route('coupons') !!}">{{trans('lang.coupon_plural')}}</a></li>
        <li class="breadcrumb-item active">{{trans('lang.coupon_edit')}}</li>
      </ol>
    </div>
  </div>
  <div>
    <div class="card-body">
      <div class="error_top" style="display:none"></div>
      <div class="row restaurant_payout_create">
        <div class="restaurant_payout_create-inner">
          <fieldset>
            <legend>{{trans('lang.coupon_edit')}}</legend>
            <div class="form-group row width-50">
              <label class="col-3 control-label">{{trans('lang.coupon_code')}}</label>
              <div class="col-7">
                <input type="text" type="text" class="form-control coupon_code">
                <div class="form-text text-muted">{{ trans("lang.coupon_code_help") }} </div>
              </div>
            </div>
            <div class="form-group row width-50">
              <label class="col-3 control-label">{{trans('lang.coupon_discount_type')}}</label>
              <div class="col-7">
                <select id="coupon_discount_type" class="form-control">
                  <option vale="Percentage">{{trans('lang.coupon_percent')}}</option>
                  <option value="Fix Price">{{trans('lang.coupon_fixed')}}</option>
                </select>
                <div class="form-text text-muted">{{ trans("lang.coupon_discount_type_help") }}</div>
              </div>
            </div>
            <div class="form-group row width-50">
              <label class="col-3 control-label">{{trans('lang.coupon_discount')}}</label>
              <div class="col-7">
                <input type="number" type="text" class="form-control coupon_discount">
                <div class="form-text text-muted">{{ trans("lang.coupon_discount_help") }}</div>
              </div>
            </div>
            <div class="form-group row width-50">
              <label class="col-3 control-label">{{trans('lang.coupon_expires_at')}}</label>
              <div class="col-7">
                <div class='input-group date' id='datetimepicker1'>
                  <input type='text' class="form-control date_picker input-group-addon" />
                  <span class="">
                  </span>
                </div>
                <div class="form-text text-muted">
                  {{ trans("lang.coupon_expires_at_help") }}
                </div>
              </div>
            </div>
            <div class="form-group row width-50">
              <label class="col-3 control-label">{{trans('lang.coupon_restaurant_id')}}</label>
              <div class="col-7">
                <select id="vendor_restaurant_select" class="form-control">
                  <option value="">{{trans('lang.select_restaurant')}}</option>
                </select>
                <div class="form-text text-muted">
                  {{ trans("lang.coupon_restaurant_id_help") }}
                </div>
              </div>
            </div>
            <div class="form-group row width-100">
              <label class="col-3 control-label">{{trans('lang.coupon_description')}}</label>
              <div class="col-7">
                <textarea rows="12" class="form-control coupon_description" id="coupon_description"></textarea>
                <div class="form-text text-muted">{{ trans("lang.coupon_description_help") }}</div>
              </div>
            </div>
            <div class="form-group row width-100">
              <label class="col-3 control-label">{{trans('lang.category_image')}}</label>
              <div class="col-7">
                <input type="file" onChange="handleFileSelect(event)">
                <div class="placeholder_img_thumb coupon_image"></div>
                <div id="uploding_image"></div>
              </div>
            </div>
            <div class="form-group row width-100">
              <div class="form-check">
                <input type="checkbox" class="coupon_enabled" id="coupon_enabled">
                <label class="col-3 control-label" for="coupon_enabled">{{trans('lang.coupon_enabled')}}</label>
              </div>
            </div>
            <div class="form-group row width-100">
              <div class="form-check">
                <input type="checkbox" class="coupon_public" id="coupon_public">
                <label class="col-3 control-label" for="coupon_public">{{trans('lang.coupon_public')}}</label>
              </div>
            </div>
          </fieldset>
        </div>
      </div>
    </div>
    <div class="form-group col-12 text-center btm-btn">
      <button type="button" class="btn btn-primary save_coupon_btn"><i class="fa fa-save"></i> {{
        trans('lang.save')}}</button>
      <a href="{!! route('coupons') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{ trans('lang.cancel')}}</a>
    </div>
  </div>
  @endsection
  @section('scripts')
  <script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
  <link href="{{ asset('css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
  <script>
    var id = "<?php echo $id; ?>";
    var database = firebase.firestore();
    var ref = database.collection('coupons').where("id", "==", id);
    var photo = "";
    var photo_coupon = "";
    var fileName = "";
    var couponImageFile = "";
    var storageRef = firebase.storage().ref('images');
    var storage = firebase.storage();
    var storage = firebase.storage();
    var placeholderImage = '';
    var placeholder = database.collection('settings').doc('placeHolderImage');
    placeholder.get().then(async function(snapshotsimage) {
      var placeholderImageData = snapshotsimage.data();
      placeholderImage = placeholderImageData.image;
    })
    $(document).ready(function() {
      $(function() {
        $('#datetimepicker1 .date_picker').datepicker({
          dateFormat: 'mm/dd/yyyy',
          startDate: new Date(), 
        });
      });
      jQuery("#data-table_processing").show();
      ref.get().then(async function(snapshots) {
        var coupon = snapshots.docs[0].data();
        await database.collection('vendors').get().then(async function(snapshots) {
          snapshots.docs.forEach((listval) => {
            var data = listval.data();
            if (data.id == coupon.resturant_id) {
              $('#vendor_restaurant_select').append($("<option selected></option>")
                .attr("value", data.id)
                .text(data.title));
            } else {
              $('#vendor_restaurant_select').append($("<option></option>")
                .attr("value", data.id)
                .text(data.title));
            }
          })
        });
        if (coupon.image != '' && coupon.image != null) {
          photo_coupon = coupon.image;
          couponImageFile = coupon.image;
          $(".coupon_image").append('<img  onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'"class="rounded" style="width:50px" src="' + photo_coupon + '" alt="image">');
        } else {
          $(".coupon_image").append('<img class="rounded" style="width:50px" src="' + placeholderImage + '" alt="image">');
        }
        $(".coupon_code").val(coupon.code);
        $("#coupon_discount_type").val(coupon.discountType);
        $(".coupon_discount").val(parseInt(coupon.discount));
        $(".coupon_description").val(coupon.description);
        const expireDate = new Date(coupon.expiresAt.toDate()); 
        const currentDate = new Date();
        const isExpired = expireDate < currentDate;
        if (isExpired){
            $(".coupon_enabled").prop("disabled", true);
        }
        else if (coupon.isEnabled) {
          $(".coupon_enabled").prop("checked", true);
        }
        if (coupon.isPublic) {
          $(".coupon_public").prop("checked", true);
        }
        if (coupon.hasOwnProperty("expiresAt")) {
          try {
            var date1 = coupon.expiresAt.toDate().toDateString();
            var date = new Date(date1);
            var dd = String(date.getDate()).padStart(2, '0');
            var mm = String(date.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = date.getFullYear();
            var expiresDate = mm + '/' + dd + '/' + yyyy;
          } catch (err) {
            var date1 = '';
            var date = '';
            var dd = '';
            var mm = '';
            var yyyy = '';
            var expiresDate = '';
          }
          var $datepicker = $('.date_picker');
          $datepicker.datepicker();
          $datepicker.datepicker('setDate', expiresDate);
        }
        var resturant = "<?php echo $id; ?>";
        $("#vendor_restaurant_select").change(function() {
          var resturant_id = $(this).val();
        });
        jQuery("#data-table_processing").hide();
      })
      $(".save_coupon_btn").click(function() {
        var code = $(".coupon_code").val();
        var discount = $(".coupon_discount").val();
        var description = $(".coupon_description").val();
        var newdate = new Date($(".date_picker").val());
        var expiresAt = new Date(newdate.setHours(23, 59, 59, 999));
        var isEnabled = $(".coupon_enabled").is(":checked");
        var isPublic = $(".coupon_public").is(":checked");
        var discountType = $("#coupon_discount_type").val();
        var resturant_id = $("#vendor_restaurant_select option:selected").val();
        var codeAlreadyExist = false;
        database.collection('coupons').where('code', '==', code).get().then(async function (snapshot) {
            if (!snapshot.empty && snapshot.docs.length > 1) {
                codeAlreadyExist = true;
            }
            if (code == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.enter_coupon_code_error')}}</p>");
                window.scrollTo(0, 0);
            } else if (discount == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.enter_coupon_discount_error')}}</p>");
                window.scrollTo(0, 0);
            } else if (discountType == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.select_coupon_discountType_error')}}</p>");
                window.scrollTo(0, 0);
            } else if (newdate == 'Invalid Date') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.select_coupon_expdate_error')}}</p>");
                window.scrollTo(0, 0);
            } else if (resturant_id == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.select_resturant_error')}}</p>");
                window.scrollTo(0, 0);
            } else if (codeAlreadyExist == true) {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.enter_coupon_code_already_exist_error')}}</p>");
                window.scrollTo(0, 0);
            } else if (discountType == "{{trans('lang.coupon_percent')}}" && (discount >= 100 || discount < 0)) {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.enter_coupon_percentage_discount_error')}}</p>");
                window.scrollTo(0, 0);
            } else {
                storeImageData().then(IMG => {
                    database.collection('coupons').doc(id).set({
                        'code': code,
                        'description': description,
                        'discount': discount,
                        'expiresAt': expiresAt,
                        'isEnabled': isEnabled,
                        'id': id,
                        'discountType': discountType,
                        'image': IMG,
                        'resturant_id': resturant_id,
                        'isPublic': isPublic
                    }).then(function (result) {
                        window.location.href = '{{ route("coupons")}}';
                    });
                }).catch(err => {
                    jQuery("#data-table_processing").hide();
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>" + err + "</p>");
                    window.scrollTo(0, 0);
                });
            }
        });
      })
    })
    async function storeImageData() {
      var newPhoto = '';
      try {
          if (couponImageFile != "" && couponImageFile != null && photo_coupon != couponImageFile) {
              var oldImageUrl = await storage.refFromURL(couponImageFile);
              imageBucket = oldImageUrl.bucket;
              var envBucket = "<?php echo env('FIREBASE_STORAGE_BUCKET'); ?>";
              if (imageBucket == envBucket) {
                  await oldImageUrl.delete().then(() => {
                      console.log("Old file deleted!")
                  }).catch((error) => {
                      console.log("ERR File delete ===", error);
                  });
              } else {
                  console.log('Bucket not matched');
              }
          }
          if (photo_coupon != couponImageFile) {
              photo_coupon = photo_coupon.replace(/^data:image\/[a-z]+;base64,/, "")
              var uploadTask = await storageRef.child(fileName).putString(photo_coupon, 'base64', { contentType: 'image/jpg' });
              var downloadURL = await uploadTask.ref.getDownloadURL();
              newPhoto = downloadURL;
              photo_coupon = downloadURL;
          } else {
              newPhoto = photo_coupon;
          }
      } catch (error) {
          console.log("ERR ===", error);
      }
      return newPhoto;
    }
    function handleFileSelect(evt) {
        var f = evt.target.files[0];
        var reader = new FileReader();
        reader.onload = (function (theFile) {
            return function (e) {
                var filePayload = e.target.result;
                var hash = CryptoJS.SHA256(Math.random() + CryptoJS.SHA256(filePayload));
                var val = f.name;
                var ext = val.split('.')[1];
                var docName = val.split('fakepath')[1];
                var filename = (f.name).replace(/C:\\fakepath\\/i, '')
                var timestamp = Number(new Date());
                var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;
                fileName = filename;
                photo_coupon = filePayload;
                $(".coupon_image").empty();
                $(".coupon_image").append('<img class="rounded" style="width:50px" src="' + photo_coupon + '" alt="image">');
            };
        })(f);
        reader.readAsDataURL(f);
    }
  </script>
  @endsection