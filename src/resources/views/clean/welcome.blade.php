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

		<div class="form-group{{ $errors->has('clear_config') ? ' has-error' : '' }}">
			<label><input type="checkbox" name="clear_config" value="1" /> @lang('assistant::clean.forms.attributes.clear_config')</label>
            @if ($errors->has('clear_config'))
				<div class="invalid-feedback d-block">{{ $errors->first('clear_config') }}</div>
			@endif
		</div>

		<div class="form-group{{ $errors->has('clear_route') ? ' has-error' : '' }}">
			<label><input type="checkbox" name="clear_route" value="1" /> @lang('assistant::clean.forms.attributes.clear_route')</label>
            @if ($errors->has('clear_route'))
				<div class="invalid-feedback d-block">{{ $errors->first('clear_route') }}</div>
			@endif
		</div>

		<div class="form-group{{ $errors->has('clear_view') ? ' has-error' : '' }}">
			<label><input type="checkbox" name="clear_view" value="1" /> @lang('assistant::clean.forms.attributes.clear_view')</label>
            @if ($errors->has('clear_view'))
				<div class="invalid-feedback d-block">{{ $errors->first('clear_view') }}</div>
			@endif
		</div>

		<div class="form-group{{ $errors->has('clear_compiled') ? ' has-error' : '' }}">
			<label><input type="checkbox" name="clear_compiled" value="1" /> @lang('assistant::clean.forms.attributes.clear_compiled')</label>
            @if ($errors->has('clear_compiled'))
				<div class="invalid-feedback d-block">{{ $errors->first('clear_compiled') }}</div>
			@endif
		</div>
	</fieldset>

	<fieldset>
		<legend>@lang('assistant::clean.forms.legends.cache')</legend>
		<div class="form-group{{ $errors->has('cache_config') ? ' has-error' : '' }}">
			<label><input type="checkbox" name="cache_config" value="1" /> @lang('assistant::clean.forms.attributes.cache_config')</label>
            @if ($errors->has('cache_config'))
				<div class="invalid-feedback d-block">{{ $errors->first('cache_config') }}</div>
			@endif
		</div>

		<div class="form-group{{ $errors->has('cache_route') ? ' has-error' : '' }}">
			<label><input type="checkbox" name="cache_route" value="1" /> @lang('assistant::clean.forms.attributes.cache_route')</label>
            @if ($errors->has('cache_route'))
				<div class="invalid-feedback d-block">{{ $errors->first('cache_route') }}</div>
			@endif
		</div>
	</fieldset>

	<fieldset>
		<legend>@lang('assistant::clean.forms.legends.optimize')</legend>
		<div class="form-group{{ $errors->has('complex_optimize') ? ' has-error' : '' }}">
			<label><input type="checkbox" name="complex_optimize" value="1" /> @lang('assistant::clean.forms.labels.complex_optimize')</label>
            @if ($errors->has('complex_optimize'))
				<div class="invalid-feedback d-block">{{ $errors->first('complex_optimize') }}</div>
			@endif
		</div>
	</fieldset>
@endsection
