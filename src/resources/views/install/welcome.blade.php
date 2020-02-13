@extends('assistant::_layouts.app')

@section('card_body')
	@lang('assistant::install.descriptions.welcome')

	<fieldset>
		<legend>@lang('assistant::install.forms.legends.welcome')</legend>
		<div class="form-group row{{ $errors->has('APP_NAME') ? ' has-error' : '' }}">
			<label class="col-sm-3 col-form-label">@lang('assistant::install.forms.labels.APP_NAME')</label>
			<div class="col-sm-9">
				<input type="text" name="APP_NAME" value="{{ old('APP_NAME', '') }}" placeholder="@lang('assistant::install.forms.placeholders.APP_NAME')" class="form-control"  />
				@if ($errors->has('APP_NAME'))
					<div class="invalid-feedback d-block">{{ $errors->first('APP_NAME') }}</div>
				@endif
			</div>
		</div>

		<div class="form-group row{{ $errors->has('licence') ? ' has-error' : '' }}">
			<div class="col-sm-9 offset-sm-3">
				<label><input type="checkbox" name="licence" value="1" /> @lang('assistant::install.forms.labels.licence')</label>
				@if ($errors->has('licence'))
					<div class="invalid-feedback d-block">{{ $errors->first('licence') }}</div>
				@endif
			</div>
		</div>
	</fieldset>
@endsection
