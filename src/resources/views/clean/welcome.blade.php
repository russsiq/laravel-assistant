@extends('assistant::_layouts.app')

@section('card_body')
	@lang('assistant::clean.descriptions.welcome')

	@if ($errors->any())
	    <div class="alert alert-danger">
	        <ul class="alert-list">
	            @foreach ($errors->all() as $error)
	                <li>{{ $error }}</li>
	            @endforeach
	        </ul>
	    </div>
	@endif

	<fieldset>
		<legend>@lang('assistant::clean.forms.legends.clear')</legend>
		<div class="form-group{{ $errors->has('clear_cache') ? ' has-error' : '' }}">
			<label><input type="checkbox" name="clear_cache" value="1" /> @lang('assistant::clean.forms.attributes.clear_cache')</label>
            @if ($errors->has('clear_cache'))
				<div class="invalid-feedback d-block">{{ $errors->first('clear_cache') }}</div>
			@endif
		</div>

		<div class="form-group{{ $errors->has('clear_view') ? ' has-error' : '' }}">
			<label><input type="checkbox" name="clear_view" value="1" /> @lang('assistant::clean.forms.attributes.clear_view')</label>
            @if ($errors->has('clear_view'))
				<div class="invalid-feedback d-block">{{ $errors->first('clear_view') }}</div>
			@endif
		</div>
	</fieldset>

	<fieldset>
		<legend>@lang('assistant::clean.forms.legends.cache')</legend>
		<div class="form-group{{ $errors->has('cache') ? ' has-error' : '' }}">
			<label><input type="checkbox" name="cache[]" value="1" /> @lang('welcome.licence.accept')</label>
            @if ($errors->has('cache'))
				<div class="invalid-feedback d-block">{{ $errors->first('cache') }}</div>
			@endif
		</div>
	</fieldset>

	<fieldset>
		<legend>@lang('assistant::clean.forms.legends.optimize')</legend>
		<div class="form-group{{ $errors->has('optimize') ? ' has-error' : '' }}">
			<label><input type="checkbox" name="optimize[]" value="1" /> @lang('welcome.licence.accept')</label>
            @if ($errors->has('optimize'))
				<div class="invalid-feedback d-block">{{ $errors->first('optimize') }}</div>
			@endif
		</div>
	</fieldset>
@endsection
