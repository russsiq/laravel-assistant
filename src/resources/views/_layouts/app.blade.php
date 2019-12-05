<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
    <title>@yield('action_title') - @lang('header.title')</title>
    <meta charset="utf-8" />
    <meta http-equiv="Cache-Control" content="no-cache" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    @include('assistant::_themes.default')

    <link href="{{ asset('favicon.ico') }}" rel="icon" type="image/x-icon" />
</head>

<body>
    <div id="app" class="container">
        <div class="assistant">
            <h1 class="assistant__header">Ассистент приложения</h1>
            <div class="assistant__body">
                <aside class="assistant__aside">
                    <ul class="aside__list">
                        @foreach (['install', 'update', 'archive', 'clean'] as $key)
                            <li class="aside_list__item">
                                <a href="{{ route("assistant.$key.welcome") }}"
                                    class="aside_list__action {{ $key === $master ? 'active' : '' }} {{ ($key === 'install' and $installed) ? 'disabled' : '' }}"
                                >@lang("aside.$key")</a>
                            </li>
                        @endforeach
                    </ul>
                </aside>

                <main class="assistant__main">
                    <form action="{{ $action }}" method="post">
                        @csrf

                        <div class="form__header">
                            <h2>@lang('header.menu.'.$stage)</h2>
                        </div>

                        <div class="form__body">
                            @yield('card_body')
                        </div>

                        <div class="form__footer">
                            @if ($onFinishStage)
                                <button type="submit" class="btn ml-auto">@lang('common.btn.finish')</button>
                            @else
                                <button type="submit" class="btn ml-auto">@lang('common.btn.next')</button>
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
