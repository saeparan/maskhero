@extends('layouts.default')

@section('content')
    <div id="map" style="width:100%;height:92%;"></div>
    <div class="fixed-bottom mb-0 row">
        <div class="col-10 offset-1 col-md-6 offset-md-3">
            <div id="div-store-info" class="card-bottom shadow-sm card shadow w-100"
                 style="margin: 0px auto; display: none;">
                <div id="btn-store-info-close" class="pull-right position-absolute font-weight-light text-md-right"
                     style="right: 1rem; top: 1rem; cursor: pointer;">
                    <span aria-hidden="true">&times;</span>
                </div>
                <div class="card-body text-gray-900">
                    <div class="row pb-1">
                        <div class="col-md-6">
                            <span id="store_badge" class="badge badge-pill badge-danger mr-1 align-middle">품절</span>
                            <span id="store_name" class="h4 font-weight-bold align-middle">토궁퉁약국</span>
                        </div>
                    </div>

                    <h6 id="store_addr" class="pb-2 font-weight-light">서울시</h6>
                    <h5 id="store_stock" class="pb-2">현재 재고 0개</h5>
                    <h6 id="store_stock_at" class="font-weight-light"></h6>
                    <span id="store_update_time">2020-02-02 14:55</span>

                    @if (date('H') > 21 || date('H') < 8)
                    <h6 id="store_stock" class="pt-2 font-weight-light">현재는 약국 운영시간이 아닐 수 있으며, 재고는 오전 8시부터 다시 업데이트 됩니다.</h6>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
    <script>
        var map = new naver.maps.Map('map', {
            center: new naver.maps.LatLng(37.4840332, 126.9300588),
            zoom: 18,
            useStyleMap: true,
        });
        var dragTimer = null;
        var markers = [];
        var stock_at = {
            'plenty': '충분',
            'some': '보통',
            'few': '부족',
            'empty': '재고없음',
        }
        var stock_text = {
            'plenty': '100개 이상 보유',
            'some': '30~99개 보유',
            'few': '2~29개 보유',
            'empty': '재고없음',
        }
        var stock_color = {
            'plenty': 'primary',
            'some': 'primary',
            'few': 'primary',
            'empty': 'danger',
        }

        function onSuccessGeolocation(position) {
            var location = new naver.maps.LatLng(position.coords.latitude,
                position.coords.longitude);

            map.setCenter(location);
            new naver.maps.Marker({
                position: new naver.maps.LatLng(position.coords.latitude, position.coords.longitude),
                map: map
            });

            get_store_set_marker(position.coords.latitude, position.coords.longitude);
        }

        function onErrorGeolocation() {
            get_store_set_marker(map.center.y, map.center.x);
        }

        naver.maps.Event.addListener(map, 'dragend', function (e) {
            get_store_set_marker(e.coord._lat, e.coord._lng)
        });

        function get_store_set_marker(lat, lng) {
            axios.get('/api/store/' + lat + '/' + lng, {})
                .then(function (response) {
                    for (var i = 0; i < markers.length; i++) {
                        markers[i].setMap(null);
                    }
                    markers = [];

                    var data = response.data;
                    for (var i = 0; i < data.length; i++) {
                        var dataItem = data[i];
                        if (dataItem.remain_stat === undefined || dataItem.remain_stat === null) {
                            dataItem.remain_stat = 'few';
                        }
                        var marker = new naver.maps.Marker({
                            position: new naver.maps.LatLng(dataItem.lat, dataItem.lng),
                            map: map,
                            icon: {
                                content: `
                                <div data-code="${dataItem.code}" class="div-store p-2 bg-${stock_color[dataItem.remain_stat]} text-white d-inline-block rounded border-dark">
                                     <h6 class="mb-1 font-weight-light">${dataItem.name}</h6>
                                     <h5 class="mb-0">${stock_at[dataItem.remain_stat]}</h5>
                                </div>
                                `,
                                size: new naver.maps.Size(38, 58),
                                anchor: new naver.maps.Point(19, 58),
                            }
                        });
                        markers.push(marker);
                    }
                })
                .catch(function (error) {

                })
                .then(function () {

                });
        }

        jQuery(function () {
            $('body').on('click', '.div-store', function () {
                var code = $(this).data('code');
                axios.get('/api/store/' + code)
                    .then(function (response) {
                        var dataItem = response.data;
                        $('#store_name').text(dataItem.name);
                        $('#store_addr').text(dataItem.addr);
                        $('#store_stock_at').text(dataItem.stock_at + ' 마스크 입고');
                        $('#store_update_time').text(dataItem.created_at + ' 기준');

                        if (dataItem.remain_stat === undefined || dataItem.remain_stat === null) {
                            dataItem.remain_stat = 'few';
                        }

                        $('#store_badge').text(stock_at[dataItem.remain_stat]).removeClass('badge-danger badge-primary').addClass('badge-' + stock_color[dataItem.remain_stat]);
                        $('#store_stock').text(stock_text[dataItem.remain_stat]);

                        $('#div-store-info').fadeIn('fast');
                    });
            });

            $('#btn-store-info-close').click(function () {
                $('#div-store-info').fadeOut('fast');
            });

            if (navigator.geolocation) {
                setTimeout(function () {
                    navigator.geolocation.getCurrentPosition(onSuccessGeolocation, onErrorGeolocation);
                }, 500);
            }
        });
    </script>
@stop
