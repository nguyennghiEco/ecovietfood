@include('layouts.app')
@include('layouts.header')
<div class="st-cats-page pt-5 bg-white category-listing-page">
    <div class="container">
        <div class="d-flex align-items-center mb-3 page-title">
            <h3 class="font-weight-bold text-dark title">
                {{trans('lang.categories')}}
            </h3>
        </div>
        <div id="brandlist"></div>
    </div>
</div>
@include('layouts.footer')
<script type="text/javascript">
    var catsRef = database.collection('vendor_categories').where('publish', '==', true).orderBy('title');
    var placeholderImageRef = database.collection('settings').doc('placeHolderImage');
    var placeholderImageSrc = '';
    placeholderImageRef.get().then(async function (placeholderImageSnapshots) {
        var placeHolderImageData = placeholderImageSnapshots.data();
        placeholderImageSrc = placeHolderImageData.image;
    })
    $(document).ready(function () {
        catsRef.get().then(async function (snapshots) {
            if (snapshots != undefined) {
                var html = '';
                html = buildHTML(snapshots);
                jQuery("#data-table_processing").hide();
                if (html != '') {
                    var append_list = document.getElementById('brandlist');
                    append_list.innerHTML = html;
                    $("#data-table_processing").hide();
                }
            }
        });
    })
    function buildHTML(nearestRestauantSnapshot) {
        var html = '';
        var alldata = [];
        nearestRestauantSnapshot.docs.forEach((listval) => {
            var datas = listval.data();
            datas.id = listval.id;
            alldata.push(datas);
        });
        var count = 0;
        var popularFoodCount = 0;
        html = html + '<div class="row">';
        alldata.forEach((listval) => {
            var val = listval;
            html = html + '<div class="col-md-2 pb-3 brand-list mb-3"><div class="list-card position-relative"><div class="list-card-image">';
            if (val.photo!="" && val.photo!=null) {
                photo = val.photo;
            } else {
                photo = placeholderImageSrc;
            }
            var view_vendor_details = "{{ route('productList',[':type',':id'])}}";
            view_vendor_details = view_vendor_details.replace(':type', 'category');
            view_vendor_details = view_vendor_details.replace(':id', val.id);
            html = html + '<a href="' + view_vendor_details + '"><img onerror="this.onerror=null;this.src=\'' + placeholderImageSrc + '\'" alt="#" src="' + photo + '" class="img-fluid item-img w-100"></a></div><div class="p-2 position-relative brand-title"><div class="list-card-body"><h6 class="mb-1"><a href="' + view_vendor_details + '" class="text-black">' + val.title + '</a></h6>';
            html = html + '</div>';
            html = html + '</div></div></div>';
        });
        html = html + '</div>';
        return html;
    }
</script>
@include('layouts.nav')
