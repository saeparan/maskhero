<!doctype html>
<html lang="en">
<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-2389884-5"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-2389884-5');
    </script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script defer src="https://use.fontawesome.com/releases/v5.0.2/js/all.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.0.7/css/sb-admin-2.min.css"
          crossorigin="anonymous">
    <link href=//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css rel=stylesheet type=text/css>
    <script type="text/javascript"
            src="https://openapi.map.naver.com/openapi/v3/maps.js?ncpClientId=dn4q87xmjw"></script>
    <title>마스크히어로</title>
    <meta name="description" content="공적마스크를 쉽고 빠르게 찾아보고, 코로나19에 대비하세요." class="next-head" />
    <meta name="twitter:title" content="마스크히어로" class="next-head" />
    <meta name="twitter:description" content="공적마스크를 쉽고 빠르게 찾아보고, 코로나19에 대비하세요." class="next-head" />
    <meta name="twitter:creator" content="maskhero" class="next-head" />
    <meta name="twitter:site" content="maskhero" class="next-head" />
    <meta name="twitter:url" content="https://maskhero.net/" class="next-head" />
    <meta property="og:title" content="마스크히어로" class="next-head" />
    <meta property="og:url" content="https://maskhero.net" class="next-head" />
    <meta property="og:site_name" content="마스크히어로" class="next-head" />
    <meta property="og:image" content="https://maskhero.net/img/mask.png" class="next-head" />
    <meta property="og:description" content="공적마스크를 쉽고 빠르게 찾아보고, 코로나19에 대비하세요." class="next-head" />
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />
    <style>
        body, html {
            height: 100%;
        }

        * {
            font-family: "Spoqa Han Sans", "Sans-serif";
        }

        .div-store {
            font-size: .7rem !important;
            text-align: center;
            min-width: 60px;
            max-width: 200px;
        }

        .div-store h6 {
            font-size: .7rem !important;
        }

        .div-store h5 {
            font-size: .8rem !important;
        }

        #div-store-info > .card-body h5 {
            font-size: 1rem !important;
        }

        #div-store-info > .card-body h6 {
            font-size: 0.8rem !important;
        }

        .card-bottom {
            border-top-left-radius: .45rem !important;
            border-top-right-radius: .45rem !important;
            border-bottom-left-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
            border: 0;
        }

        #map .map .cluster {
            background-color: #5963D9;
            z-index: 11;
            overflow: visible !important;
            text-align: center;
            border: 1px solid rgba(0, 0, 0, 0.1);
            -webkit-box-shadow: 1px 1px 2px 1px rgba(0, 0, 0, 0.2);
            -moz-box-shadow: 1px 1px 2px 1px rgba(0, 0, 0, 0.2);
            box-shadow: 1px 1px 2px 1px rgba(0, 0, 0, 0.2);
            -webkit-border-radius: 3px;
            -moz-border-radius: 3px;
            -ms-border-radius: 3px;
            -o-border-radius: 3px;
            border-radius: 3px;
            font-family: 'SpoqaHanSans', 'SpoqaHanSansWeb';
            font-variant-ligatures: none;
            -webkit-font-variant-ligatures: none;
            -moz-osx-font-smoothing: grayscale;
            font-smoothing: antialiased;
            -webkit-font-smoothing: antialiased;
        }

        .bd-example-modal-lg .modal-dialog{
            display: table;
            position: relative;
            margin: 0 auto;
            top: calc(50% - 24px);
        }

        .bd-example-modal-lg .modal-dialog .modal-content{
            background-color: transparent;
            border: none;
        }
    </style>
</head>
<body class="bg-light">

<div class="container-fluid m-0 p-0" style="height: 100%;">
    <div class="spinner-modal modal fade bd-example-modal-lg" data-backdrop="static" data-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content" style="width: 48px">
                <div class="spinner-border text-light" style="width: 3rem; height: 3rem;" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg bg-primary navbar-dark">
        <a class="navbar-brand" href="#">마스크히어로</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02"
                aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
            <ul class="navbar-nav mr-auto mt-1 mt-lg-0">
                <li class="nav-item active">
                    <a class="nav-link" href="/about">이용안내</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="/">지도</a>
                </li>
{{--                <li class="nav-item active">--}}
{{--                    <a class="nav-link" href="/stores">판매처 목록</a>--}}
{{--                </li>--}}
            </ul>
        </div>
    </nav>

    @yield('content')
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/3.3.7+1/js/sb-admin-2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/metisMenu/3.0.5/metisMenu.min.js"></script>
{{--<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>--}}
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
@yield('script')
</body>
</html>
