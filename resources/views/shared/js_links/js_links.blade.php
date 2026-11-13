
<script src="{{ asset('public/template/plugins/jquery/jquery.min.js') }}"></script>

<!-- Bootstrap 4 -->
<script src="{{ asset('public/template/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<!-- bs-custom-file-input -->
<script src="{{ asset('public/template/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>

<!-- AdminLTE App -->
<script src="{{ asset('public/template/dist/js/adminlte.min.js') }}"></script>

<!-- AdminLTE for demo purposes -->
<script src="{{ asset('public/template/dist/js/demo.js') }}"></script>

<!-- DataTables -->
<script src="{{ asset('public/template/plugins/datatables/jquery.dataTables.js') }}"></script>
<script src="{{ asset('public/template/plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>

<!-- Select2 -->
<script src="{{ asset('public/template/plugins/select2/js/select2.full.min.js') }}"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

<!-- Toastr -->
<script src="{{ asset('public/template/plugins/toastr/toastr.min.js') }}"></script>

{{-- <script src="https://cdn.jsdelivr.net/npm/pace-js@latest/pace.min.js"></script> --}}

<!-- 2️⃣ Setup iframe source -->
<script>
    @if(request()->has('source'))
        window.IFRAME_SOURCE = "{{ request('source') }}";
        sessionStorage.setItem('iframe_source', "{{ request('source') }}");
    @else
        window.IFRAME_SOURCE = sessionStorage.getItem('iframe_source');
    @endif
</script>

<!-- 3️⃣ Inject header BEFORE other scripts -->
<script>
    //clark code 02/13/2026
    // window.IFRAME_SOURCE = "{{ request('source') ?? '' }}";
    // console.log('testing 2134', window.IFRAME_SOURCE);
    if (window.self !== window.top && window.IFRAME_SOURCE) {
        $.ajaxSetup({
            headers: {
                'X-Iframe-Mode': 1,
                'X-Iframe-Source': window.IFRAME_SOURCE
            }
        });

        console.log("Iframe mode enabled:", window.IFRAME_SOURCE);
    }
</script>

<!-- Custom JS -->
{{-- <script src="{{ asset('public/js/my_js/UserLevel.js') }}"></script>
<script src="{{ asset('public/js/my_js/licence.js') }}"></script> --}}
{{-- <script src="{{ asset('public/js/my_js/User.js?n=6') }}"></script> --}}
{{-- <script src="{{ asset('public/js/my_js/inspector.js?n=108') }}"></script>
<script src="{{ asset('public/js/my_js/MaterialHandler.js?n=39') }}"></script>
<script src="{{ asset('public/js/my_js/warehouse.js?n=123') }}"></script>
<script src="{{ asset('public/js/my_js/common.js?n=16') }}"></script> --}}

<script src="@php echo asset("public/js/my_js/User.js?".date("YmdHis")) @endphp"></script>
<script src="@php echo asset("public/js/my_js/inspector.js?".date("YmdHis")) @endphp"></script>
<script src="@php echo asset("public/js/my_js/MaterialHandler.js?".date("YmdHis")) @endphp"></script>
<script src="@php echo asset("public/js/my_js/warehouse.js?".date("YmdHis")) @endphp"></script>
<script src="@php echo asset("public/js/my_js/common.js?".date("YmdHis")) @endphp"></script>
