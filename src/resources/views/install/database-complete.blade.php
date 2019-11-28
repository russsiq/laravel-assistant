@extends('assistant::_layouts.app')

@section('action_title', __('header.menu.database-complete'))

@section('card_body')
	<fieldset>
		@if ($messages = session('messages'))
			<p>@lang('database.migrate.success')</p>
			<pre class="alert alert-dark">{{ $messages['migrate'] }}</pre>
			@foreach ($messages['seeds'] as $message)
				<pre class="alert alert-dark">{{ $message }}</pre>
			@endforeach
		@else
			<div class="alert alert-danger">Прямой доступ на данную страницу запрещен.</div>
		@endif
	</fieldset>
@endsection
