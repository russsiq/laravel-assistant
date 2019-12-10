@extends('assistant::_layouts.app')

@section('card_body')
	@if ($messages = session('messages'))
		@lang('assistant::clean.descriptions.complete')
		<div class="alert alert-info">
			<ul class="alert-list">
				@foreach ($messages as $message)
					<li>{{ $message }}</li>
				@endforeach
			</ul>
		</div>
	@else
		<div class="alert alert-danger">@lang('assistant::assistant.messages.denied_page')</div>
	@endif
@endsection
