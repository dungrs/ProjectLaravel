
<footer class="footer">
    <div class="uk-container uk-container-center">
        <div class="footer-upper">
            <div class="uk-grid uk-grid-medium">
                <div class="uk-width-large-1-5">
                    <div class="footer-contact">
                        <a href="" class="image img-scaledown"><img src="https://themepanthers.com/wp/nest/d1/wp-content/uploads/2022/02/logo.png" alt=""></a>
                        <div class="footer-slogan">Awesome grocery store website template</div>
                        <div class="company-address">
                            <div class="address">Số 16 Ngõ 198 Lê Trọng Tấn, Khương Mai, Thanh Xuân, Hà Nội</div>
                            <div class="phone">Hotline: 0988.778.688</div>
                            <div class="email">Email: info@nestmart.com</div>
                            <div class="hour">Giờ làm việc: 10:00 - 18:00, Mon - Sat</div>
                        </div>
                    </div>
                </div>
                <div class="uk-width-large-3-5">
                    @isset($menus['menu-footer'])
                    <div class="footer-menu">
                        <div class="uk-grid uk-grid-medium">
                            @foreach ($menus['menu-footer'] as $menu)
                                <div class="uk-width-large-1-4">
                                    <div class="ft-menu">
                                        <div class="heading">{{ $menu['item']->name }}</div>
                                        @isset($menu['children'])
                                            <ul class="uk-list uk-clearfix">
                                                @foreach ($menu['children'] as $menuChild)
                                                    <li><a href="">{{ $menuChild['item']->name }}</a></li>
                                                @endforeach
                                            </ul>
                                        @endisset
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endisset
                </div>
                <div class="uk-width-large-1-5">
                    <div class="fanpage-facebook">
                        <div class="ft-menu">
                            <div class="heading">Fanpage Facebook</div>
                            <div class="fanpage">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="copyright">
        <div class="uk-container uk-container-center">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <div class="copyright-text">{!! $systems['homepage_copyright'] !!}</div>
                <div class="copyright-contact">
                    <div class="uk-flex uk-flex-middle">
                        <div class="phone-item">
                            <div class="p">Hotline: {{ $systems['contact_hotline'] }}</div>
                            <div class="worktime">Làm việc: 8:00 - 22:00</div>
                        </div>
                        <div class="phone-item">
                            <div class="p">Support: {{ $systems['contact_phone'] }}</div>
                            <div class="worktime">Hỗ trợ 24/7</div>
                        </div>
                    </div>
                </div>
                <div class="social">
                    <div class="uk-flex uk-flex-middle">
                        <div class="span">Follow us:</div>
                        <div class="social-list">
                            @php
                                $social = ['facebook', 'twitter', 'youtube']
                            @endphp
                            <div class="uk-flex uk-flex-middle">
                                @foreach ($social as $key => $val)
                                    <a target="_blank" href="{{ $systems['social_' . $val] }}" class=""><i class="fa fa-{{ $val }}"></i></a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>