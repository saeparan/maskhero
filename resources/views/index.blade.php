@extends('layouts.default')

@section('content')

    <div id="map" style="width:100%;height:92%;"></div>
    <div class="fixed-bottom mb-0 row">
        <div class="col-10 offset-1 col-md-6 offset-md-3">
            <div id="div-store-info" class="card-bottom shadow-sm card shadow w-100"
                 style="margin: 0px auto; display: none;">
                <div id="btn-store-info-close" class="pull-right position-absolute font-weight-light text-md-right"
                     style="font-size: 1.34rem; right: 1rem; top: 1rem; cursor: pointer;">
                    <span aria-hidden="true">&times;</span>
                </div>
                <div class="card-body text-gray-900">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="row pb-1">
                        <div class="col-md-6">
                            <span id="store_badge" class="badge badge-pill badge-danger mr-1 align-middle">품절</span>
                            <span id="store_name" class="h4 font-weight-bold align-middle">토궁퉁약국</span>
                        </div>
                    </div>

                    <h5 id="store_addr" class="pb-2 font-weight-light">서울시</h5>
                    <h4 id="store_stock" class="pb-2">현재 재고 0개</h4>
                    <h5 id="store_stock_at" class="font-weight-light"></h5>
                    <h5 id="store_update_time" class="font-weight-light">2020-02-02 14:55</h5>

                    @if (date('H') > 21 || date('H') < 8)
                    <h5 id="store_stock" class="pt-2 font-weight-light">현재는 약국 운영시간이 아닐 수 있으며, 재고는 오전 8시부터 다시 업데이트 됩니다.</h5>
                    @endif

                    <h6 id="" class="pt-4 font-weight-light">일선에서 노고에 고생이 많으신 약사님들께 응원과 격려 부탁드립니다.</h6>
                    <h6 id="" class="font-weight-light">데이터상 실제 재고와 10분 이상 차이가 있을 수 있으므로, 약국에 항의는 하지 말아주세요.</h6>
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
            'none': '입고전',
            'break': '판매중지',
        }
        var stock_text = {
            'plenty': '100개 이상 보유',
            'some': '30~99개 보유',
            'few': '2~29개 보유',
            'empty': '재고없음',
            'none': '오늘 입고 안됨',
            'break': '업체 사정으로 판매중지',
        }
        var stock_color = {
            'plenty': 'success',
            'some': 'primary',
            'few': 'primary',
            'empty': 'danger',
            'none': 'dark',
            'break': 'danger',
        }
        var today = new Date();
        today.setHours(0,0,0,0);

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
            $('.spinner-modal').modal('show');

            axios.get('/api/store/' + lat + '/' + lng, {})
                .then(function (response) {
                    for (var i = 0; i < markers.length; i++) {
                        markers[i].setMap(null);
                    }
                    markers = [];

                    var data = response.data;
                    for (var i = 0; i < data.length; i++) {
                        var dataItem = data[i];
                        if( new Date(dataItem.stock_at) < today || dataItem.stock_at === null ) {
                            dataItem.remain_stat = 'none';
                        }
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
                    spinner_close();
                })
                .catch(function (error) {
                    spinner_close();
                })
                .then(function () {
                    spinner_close();
                });
        }

        jQuery(function () {
            $('body').on('click', '.div-store', function () {
                $('#div-store-info .spinner-border').show();
                var code = $(this).data('code');
                axios.get('/api/store/' + code)
                    .then(function (response) {
                        $('#div-store-info .spinner-border').hide();

                        var dataItem = response.data;

                        if (dataItem.remain_stat === undefined || dataItem.remain_stat === null) {
                            dataItem.remain_stat = 'few';
                        }
                        if( new Date(dataItem.stock_at) < today || dataItem.stock_at === null ) {
                            dataItem.remain_stat = 'none';
                        }

                        $('#store_name').text(dataItem.name);
                        $('#store_addr').text(dataItem.addr);

                        if( dataItem.stock_at_text == 0 ) {
                            dataItem.stock_at_text = '입고일 확인 불가';
                        } else {
                            dataItem.stock_at_text = dataItem.stock_at_text + ' 마스크 입고';
                        }
                        $('#store_stock_at').text(dataItem.stock_at_text);
                        $('#store_update_time').text(dataItem.created_at_text + ' 기준');

                        $('#store_badge').text(stock_at[dataItem.remain_stat]).removeClass('badge-danger badge-primary').addClass('badge-' + stock_color[dataItem.remain_stat]);
                        $('#store_stock').text(stock_text[dataItem.remain_stat]);

                        $('#div-store-info').fadeIn('fast');
                    });
            });

            $('#btn-store-info-close').click(function () {
                $('#div-store-info').fadeOut('fast');
            });

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(onSuccessGeolocation, onErrorGeolocation);
            }
        });

        function spinner_close() {
            setTimeout(function() {
                $('.spinner-modal').modal('hide');
            }, 555);
        }
    </script>
@stop
