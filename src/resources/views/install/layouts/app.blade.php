<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <title>@yield('action_title') - {{ __('header.title') }}</title>
        <meta charset="utf-8" />
        <meta http-equiv="Cache-Control" content="no-cache" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <link href="{{ skin('css/app.css') }}" rel="stylesheet" />
        <link href="{{ skin('css/install.css') }}" rel="stylesheet" />
        <link href="{{ skin('favicon.ico') }}" rel="icon" type="image/x-icon">
    </head>
    <body>
        <div id="app">
            <main role="main" class="container">
                <div class="card assistant">
                    <h5 class="card-header">Ассистент приложения BixBite</h5>
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-sm-3 pr-0">
                                <div class="list-group list-group-flush">
                                    <a href="#" class="list-group-item list-group-item-action active">Установка</a>
                                    <a href="#" class="list-group-item list-group-item-action disabled">Обновление</a>
                                    <a href="#" class="list-group-item list-group-item-action disabled">Архивация</a>
                                    <a href="#" class="list-group-item list-group-item-action disabled">Очистка</a>
                                    <a href="#" class="list-group-item list-group-item-action disabled">Параметры</a>
                                </div>
                            </div>
                            <div class="col-sm-9 pl-0">
                                <form action="{{ route('assistant.install.step_choice', ['action' => $action]) }}" method="post" class="card-form">
                                    @csrf

                                    <div class="card-header d-flex justify-content-around">
                                        <div class="form-progress">
                                            <div class="form-progress-line" style="width: {{ round(100 * $curstep / count($steps), 5) }}%;"></div>
                                        </div>
                                        @foreach ($steps as $key => $step)
                                            <div class="form-step{{ $key == $curstep ? ' active' : '' }}{{ $key < $curstep ? ' activated' : '' }}">
                                                <div class="form-step-icon"></div>
                                                <p>@lang('header.menu.' . $step)</p>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="card-body">
                                        @yield('card_body')
                                    </div>

                                    <div class="card-footer d-flex">
                                        @if (1 == $curstep)
                                            <div class="btn-group">
                                                <a href="{{ route('assistant.install.step_choice', ['app_locale'=>'ru']) }}" class="btn">Русский</a>
                                                {{-- <a href="{{ route('assistant.install.step_choice', ['app_locale'=>'en']) }}" class="btn">English</a> --}}
                                            </div>
                                            <button type="submit" class="btn ml-auto">@lang('common.btn.continue')</button>
                                        @else
                                            <div class="btn- group ml-auto">
                                                @if ((count($steps) - 1) == $curstep)
                                                    <button type="submit" class="btn">@lang('common.btn.finish')</button>
                                                @elseif (count($steps) > $curstep)
                                                    <button type="submit" class="btn">@lang('common.btn.next')</button>
                                                @elseif (count($steps) == $curstep)
                                                    <a href="{{ route('panel') }}" class="btn">@lang('common.btn.continue')</a>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- <div class="row">
                    <div class="col-sm-10 offset-sm-1">
                        @include('install.components.alert_section')
                    </div>
                </div> --}}

            </main>
        </div>
        <!-- Scripts -->
        {{-- <script src="{{ skin('js/app.js') }}" type="text/javascript"></script> --}}
    </body>
</html>
