@@ -0,0 +1,77 @@
<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
    <title>@yield('action_title') - @lang('header.title')</title>
    <meta charset="utf-8" />
    <meta http-equiv="Cache-Control" content="no-cache" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    @include('assistant::themes.default')

    <link href="{{ asset('favicon.ico') }}" rel="icon" type="image/x-icon" />
</head>

<body>
    <div id="app" class="container">
        <div class="assistant">
            <h1 class="assistant__header">Ассистент приложения</h1>
            <div class="assistant__body">
                <aside class="assistant__aside">
                    <ul class="aside__list">
                        <li class="aside_list__item"><a href="{{ route('assistant.install.step_choice') }}" class="aside_list__action active">Установка</a></li>
                        <li class="aside_list__item"><a href="#" class="aside_list__action disabled">Обновление</a></li>
                        <li class="aside_list__item"><a href="#" class="aside_list__action disabled">Архивация</a></li>
                        <li class="aside_list__item"><a href="#" class="aside_list__action">Очистка</a></li>
                        <li class="aside_list__item"><a href="#" class="aside_list__action">Параметры</a></li>
                    </ul>
                </aside>

                <main class="assistant__main">
                    <form action="{{ route('assistant.install.step_choice', compact('action')) }}" method="post">
                        @csrf

                        <div class="form__header">
                            <div class="form-progress">
                                <div class="form-progress-line" style="width: {{ round(100 * $curstep / ($count = count($steps)), 5) }}%;"></div>
                            </div>
                            @foreach ($steps as $key => $step)
                                <div class="form-step{{ $key == $curstep ? ' active' : '' }}{{ $key < $curstep ? ' activated' : '' }}">
                                    <div class="form-step-icon"></div>
                                    <p>@lang('header.menu.' . $step)</p>
                                </div>
                            @endforeach
                        </div>

                        <div class="form__body">
                            @yield('card_body')
                        </div>

                        <div class="form__footer">
                            @if (1 == $curstep)
                                {{-- <div class="btn-group">
                                    <a href="{{ route('assistant.install.step_choice', ['app_locale'=>'ru']) }}" class="btn">Русский</a>
                                </div> --}}
                                <button type="submit" class="btn ml-auto">@lang('common.btn.continue')</button>
                            @else
                                <div class="btn-group ml-auto">
                                    @if (($count - 1) == $curstep)
                                        <button type="submit" class="btn">@lang('common.btn.finish')</button>
                                    @elseif ($count > $curstep)
                                        <button type="submit" class="btn">@lang('common.btn.next')</button>
                                    @elseif ($count == $curstep)
                                        <a href="{{ route('panel') }}" class="btn">@lang('common.btn.continue')</a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </form>
                </main>
            </div>
            {{-- <div class="assistant__footer"></div> --}}
        </div>
    </div>
</body>
</html>
