@extends('assistant::_layouts.app')

@section('action_title', __('header.menu.migrate'))

@section('card_body')
	<fieldset>
		@if ($message = session('status'))
			<p>@lang('migrate.textblock')</p>
			<pre id="migrate" class="alert alert-dark">{{ $message }}</pre>
		@else
			<div id="migrate" class="alert alert-danger">Прямой доступ на данную страницу запрещен.</div>
		@endif
	</fieldset>
@endsection
