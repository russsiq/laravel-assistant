@extends('assistant::_layouts.app')

@section('card_body')
	@lang('assistant::install.descriptions.welcome')

	<fieldset>
		<div class="form-group{{ $errors->has('licence') ? ' has-error' : '' }}">
			<label><input type="checkbox" name="licence" value="1" /> @lang('assistant::install.forms.labels.licence')</label>
            @if ($errors->has('licence'))
				<div class="invalid-feedback d-block">{{ $errors->first('licence') }}</div>
			@endif
		</div>
	</fieldset>
@endsection
