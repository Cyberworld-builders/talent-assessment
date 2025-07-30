<!-- Main Footer -->
<!-- Choose between footer styles: "footer-type-1" or "footer-type-2" -->
<!-- Add class "sticky" to  always stick the footer to the end of page (if page contents is small) -->
<!-- Or class "fixed" to  always fix the footer to the end of page -->
{{--<footer class="main-footer sticky footer-type-1">--}}

    {{--<div class="footer-inner">--}}

        {{--<!-- Add your copyright text here -->--}}
        {{--<div class="footer-text">--}}
            {{--&copy; {{ date('Y') }}--}}
            {{--The AOE Group--}}
        {{--</div>--}}
        {{----}}
    {{--</div>--}}

{{--</footer>--}}
@if (!$task)
    <footer>
        <img src="{{ asset('assets/images/powered-by-aoe.png') }}" />
    </footer>
@endif