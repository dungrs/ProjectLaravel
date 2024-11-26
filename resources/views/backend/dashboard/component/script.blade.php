<!-- Mainly scripts -->
<script src="{{ asset('backend/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('backend/js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>
<script src="{{ asset('backend/js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
<script src="{{ asset('backend/library/library.js') }}"></script>
<script src="{{ asset('backend/plugin/jquery-ui.js') }}"></script>

<script src="{{ asset('backend/js/plugins/flot/jquery.flot.js') }}"></script>
<script src="{{ asset('backend/js/plugins/flot/jquery.flot.tooltip.min.js') }}"></script>
<script src="{{ asset('backend/js/plugins/flot/jquery.flot.spline.js') }}"></script>
<script src="{{ asset('backend/js/plugins/flot/jquery.flot.resize.js') }}"></script>
<script src="{{ asset('backend/js/plugins/flot/jquery.flot.pie.js') }}"></script>
<script src="{{ asset('backend/js/plugins/flot/jquery.flot.symbol.js') }}"></script>
<script src="{{ asset('backend/js/plugins/flot/jquery.flot.time.js') }}"></script>
<script src="{{ asset('backend/js/plugins/peity/jquery.peity.min.js') }}"></script>
<script src="{{ asset('backend/js/demo/peity-demo.js') }}"></script>
<script src="{{ asset('backend/js/inspinia.js') }}"></script>
<script src="{{ asset('backend/js/plugins/pace/pace.min.js') }}"></script>
<script src="{{ asset('backend/js/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('backend/js/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js') }}"></script>
<script src="{{ asset('backend/js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
<script src="{{ asset('backend/js/plugins/easypiechart/jquery.easypiechart.js') }}"></script>
<script src="{{ asset('backend/js/plugins/sparkline/jquery.sparkline.min.js') }}"></script>
<script src="{{ asset('backend/js/demo/sparkline-demo.js') }}"></script>

@if (isset($config['js']) && is_array($config['js']))
    @foreach ($config['js'] as $key => $val)
        <script src="{{ asset($val) }}"></script>
    @endforeach
@endif
