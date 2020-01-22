@extends('assistant::_layouts.app')

@section('card_body')
	@lang('assistant::update.descriptions.welcome')

	@if ($errors->any())
	    <div class="alert alert-danger">
	        <ul class="alert-list">
	            @foreach ($errors->all() as $error)
	                <li>{{ $error }}</li>
	            @endforeach
	        </ul>
	    </div>
	@endif

	<div class="alert alert-info">
		<ul class="alert-list">
			<li>@lang('assistant::update.strings.installed_date_time', compact('installed_at'))</li>
			<li>@lang('assistant::update.strings.currently_version', compact('currently_version'))</li>
			@if ($available_version)
				<li>@lang('assistant::update.strings.available_version', compact('available_version'))</li>
			@endif
		</ul>
	</div>

	<fieldset>
		<legend>@lang('assistant::update.forms.legends.clear')</legend>
		<div class="form-group{{ $errors->has('clear_cache') ? ' has-error' : '' }}">
			<label><input type="checkbox" name="clear_cache" value="1" /> @lang('assistant::update.forms.attributes.clear_cache')</label>
            @if ($errors->has('clear_cache'))
				<div class="invalid-feedback d-block">{{ $errors->first('clear_cache') }}</div>
			@endif
		</div>
	</fieldset>
@endsection
