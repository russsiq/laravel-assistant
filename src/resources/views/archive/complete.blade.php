@extends('assistant::_layouts.app')

@section('card_body')
	@if ('success' === session('status'))
		@lang('assistant::archive.descriptions.complete')
		<div class="alert alert-info">
			<ul class="alert-list">
				<li>@lang('assistant::archive.strings.currently_version', compact('currently_version'))</li>
			</ul>
		</div>
	@else
		<div class="alert alert-danger">@lang('assistant::assistant.messages.errors.denied_page')</div>
	@endif
@endsection
