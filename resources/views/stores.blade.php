@extends('layouts.default')

@section('content')
    <div id="stores" class="row my-5 text-gray-900">
        <div class="col-10 offset-1 col-md-6 mt-md-3">
            <h1 class="text-weight-bold">판매처 목록</h1>
            <h5 class="mb-3 font-weight-light">표 형태로 판매처를 보여드릴게요.</h5>
        </div>

        <div class="col-10 offset-1 col-md-6 mt-3 font-weight-light">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            보여드릴 위치를 지정해보세요.
                        </div>
                        <div class="col-6 mt-3">
                            <button type="button" class="btn btn-outline-primary btn-sm" v-on:click="geoLocation">
                                <i class="fas fa-location-arrow fa-fw"></i> 내 주변 찾기
                            </button>
                        </div>
                        <div class="col-6 mt-3 text-right">
                            <div class="btn-group" role="group" aria-label="Basic example">
                                <button type="button" class="btn btn-outline-primary btn-sm" v-on:click="zoomout">글씨크기 줄임</button>
                                <button type="button" class="btn btn-outline-primary btn-sm" v-on:click="zoomin">늘림</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="stores.length" class="col-10 offset-1 col-md-6 my-2 font-weight-light text-right">
            @{{ update_time }}
        </div>
        <div v-if="stores.length" class="col-10 offset-1 col-md-6 mt-1 font-weight-light">
            <div class="card" class="mt-5">
                <ul class="list-group list-group-flush">
                    <li v-for="store in stores" class="list-group-item flex-column align-items-start">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">@{{ store.name }}</h5>
                            <small>@{{ parseFloat(store.distance).toFixed(1) }} KM</small>
                        </div>
                        <p class="mb-2">@{{ store.addr }}</p>
                        <p class="mb-0" v-if="store.remain_stat" >
                            <i v-bind:class="`text-${stock_color[store.remain_stat]}`" class="fas fa-circle fa-xs align-baseline"></i>
                            @{{ stock_at[store.remain_stat] }}
                            (@{{ stock_text[store.remain_stat] }})
                        </p>
                        <small>

                        </small>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@stop

@section('script')
    <script>
        var vm = new Vue({
            el: '#stores',
            mounted: function () {
                this.$nextTick(function () {
                    this.geoLocation();
                })
            },
            data: {
                stores: [],
                stock_at: stock_at,
                stock_color: stock_color,
                stock_text: stock_text,
                seemSize: parseInt(1),
                zoomSize: parseInt(1),
                update_time: '',
            },
            methods: {
                zoomout: () => {
                    vm.seemSize -= 0.05;
                    vm.zoomSize /= 1.2;
                    vm.zoom();
                },
                zoomin: () => {
                    vm.seemSize += 0.05;
                    vm.zoomSize = vm.zoomSize * 1.2;
                    vm.zoom();
                },
                zoom: () => {
                    let seemSize = vm.seemSize;
                    let zoomSize = vm.zoomSize;
                    let browser = navigator.userAgent.toLowerCase();

                    if (browser.indexOf("firefox") != -1) {
                        document.body.style.webkitTransform =    'scale('+seemSize+')';
                        document.body.style.webkitTransformOrigin = '50% 0 0'; //늘리고 줄였을때위치,
                        document.body.style.msTransform =   'scale('+seemSize+')';
                        document.body.style.msTransformOrigin = '50% 0 0';
                        document.body.style.transform = 'scale('+seemSize+')';
                        document.body.style.transformOrigin='50% 0 0';
                        document.body.style.OTransform = 'scale('+seemSize+')';
                        document.body.style.OTransformOrigin='50% 0 0';
                    }else{
                        document.body.style.zoom = zoomSize;
                    }
                },
                geoLocation: () => {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(vm.fetchData, () => {

                        });
                    }
                },
                fetchData: (position) => {
                    axios.get('/api/store/list/' + position.coords.latitude + '/' + position.coords.longitude, {})
                        .then(function (response) {
                            if( response.data.length > 0 ) {
                                vm.stores = response.data;

                                let t1 = moment(response.data[0].update_time);
                                let t2 = moment();


                                if( t2.hour() > 22 || t2.hour() < 8 ) {
                                    vm.update_time = '매일 8시 부터 22시까지만 데이터가 제공됩니다.';
                                } else {
                                    vm.update_time = parseInt(moment.duration(t2.diff(t1)).asMinutes()) + '분 전 업데이트됨.';
                                }
                            }
                        });
                }
            }
        });
    </script>
@stop
