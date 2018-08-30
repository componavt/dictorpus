<div class='footer'>
    <div class="container">
        <div class="row">
            <div class="col-sm-7">
@include('footer.bottom_menu')
            </div>
            <div class="col-sm-5 footer-right-col">
                <div class="developers">
                    {!! trans('blob.developers') !!}
                </div>
                <div class="license">
                    {!! trans('blob.license') !!}
                </div>
            </div>
        </div>
        <div class="copyright">
            <div class="copyright-b"><span class="copy-left">©</span> VepKar. {{ trans('main.site_title') }}</div> 
        </div>
    </div>
</div>