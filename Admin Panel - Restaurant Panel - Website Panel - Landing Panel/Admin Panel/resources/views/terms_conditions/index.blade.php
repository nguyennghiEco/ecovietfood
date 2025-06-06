@extends('layouts.app')
@section('content')
<div class="page-wrapper">
	<div class="row page-titles">
		<div class="col-md-5 align-self-center">
			<h3 class="text-themecolor">{{trans('lang.terms_and_conditions')}}</h3>
		</div>
		<div class="col-md-7 align-self-center">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
				<li class="breadcrumb-item active">{{trans('lang.terms_and_conditions')}}</li>
			</ol>
		</div>
	</div>
	<div>
	<div class="card-body">
		<div class="error_top"></div>
		<div class="terms-cond restaurant_payout_create row">
			<div class="restaurant_payout_create-inner">
				<fieldset>
					<legend>{{trans('lang.terms_and_conditions')}}</legend>
					<div class="form-group width-100 row">
					  <div class="col-7">
						<textarea class="form-control col-7" name="terms_and_conditions" id="terms_and_conditions"></textarea>
					  </div>
					</div>
				</fieldset>
			</div>
		</div>
	</div>
	<div class="form-group col-12 text-center btm-btn" >
		<button type="button" class="btn btn-primary  edit-setting-btn" ><i class="fa fa-save"></i> {{ trans('lang.save')}}</button>
		<a href="{!! route('users') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{ trans('lang.cancel')}}</a>
	</div>
</div>
@endsection
@section('scripts')
<script>
var database = firebase.firestore();
var photo ="";
var ref = database.collection('settings').doc('termsAndConditions');

$(document).ready(function () {
	jQuery("#data-table_processing").show();
	ref.get().then(async function (snapshots) {
		var user = snapshots.data();
		if(user.termsAndConditions)
		{
			$('#terms_and_conditions').summernote("code", user.termsAndConditions);
		}
		jQuery("#data-table_processing").hide();
	});
	
	$('#terms_and_conditions').summernote({
		height: 400,
		width: 1000,
		toolbar: [
				['style', ['bold', 'italic', 'underline', 'clear']],
				['font', ['strikethrough', 'superscript', 'subscript']],
				['fontsize', ['fontsize']],
				['color', ['color']],
				['forecolor', ['forecolor']],
				['backcolor', ['backcolor']],
				['para', ['ul', 'ol', 'paragraph']],
				['height', ['height']]
			]
    });
	
	$(".edit-setting-btn").click(function(){
	 	var terms_and_conditions =  $('#terms_and_conditions').summernote('code');
	    if(terms_and_conditions == ''){
	        $(".error_top").show();
	        $(".error_top").html("");
	        $(".error_top").append("<p>{{trans('lang.user_firstname_error')}}</p>");
	        window.scrollTo(0, 0);
	  	}else{
	        database.collection('settings').doc('termsAndConditions').update({'termsAndConditions':terms_and_conditions}).then(function(result) {
					window.location.href = '{{ route("termsAndConditions")}}';
	        })
	    }
	})
});
</script>
@endsection
