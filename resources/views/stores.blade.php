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
                        <div class="col-12 mt-3">
                            <button type="button" class="btn btn-outline-primary btn-sm" v-on:click="geoLocation">
                                <i class="fas fa-location-arrow fa-fw"></i> 내 위치로 지정
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="stores.length" class="col-10 offset-1 col-md-6 mt-3 font-weight-light">
            <div class="card" class="mt-5">
                <ul class="list-group list-group-flush">
                    <li v-for="store in stores" class="list-group-item flex-column align-items-start">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">@{{ store.name }}</h5>
                            <small>@{{ parseFloat(store.distance * 100).toFixed(1) }} KM</small>
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
            },
            methods: {
                geoLocation: () => {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(vm.fetchData, () => {

                        });
                    } else {

                    }
                },
                fetchData: (position) => {
                    axios.get('/api/store/list/' + position.coords.latitude + '/' + position.coords.longitude, {})
                        .then(function (response) {
                            if( response.data.length > 0 ) {
                                vm.stores = response.data;
                            }
                        });
                }
            }
        })
    </script>
@stop
