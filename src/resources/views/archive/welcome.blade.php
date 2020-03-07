@extends('assistant::_layouts.app')

@section('card_body')
	@lang('assistant::archive.descriptions.welcome')

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
		<legend>@lang('assistant::archive.forms.legends.backup')</legend>

		<div class="form-group @error('backup_uploads') is-invalid @enderror">
			<label><input type="radio" name="backup" value="complex" @checked /> @lang('assistant::archive.forms.attributes.backup_complex')</label>
			@error ('backup_uploads')
				<div class="invalid-feedback d-block">{{ $message }}</div>
			@enderror
		</div>

		<div class="form-group @error('backup_database') is-invalid @enderror">
			<label><input type="radio" name="backup" value="database" /> @lang('assistant::archive.forms.attributes.backup_database')</label>
			@error ('backup_database')
				<div class="invalid-feedback d-block">{{ $message }}</div>
			@enderror
		</div>

		<div class="form-group @error('backup_system') is-invalid @enderror">
			<label><input type="radio" name="backup" value="system" /> @lang('assistant::archive.forms.attributes.backup_system')</label>
			@error ('backup_system')
				<div class="invalid-feedback d-block">{{ $message }}</div>
			@enderror
		</div>

		<div class="form-group @error('backup_theme') is-invalid @enderror">
			<label><input type="radio" name="backup" value="theme" /> @lang('assistant::archive.forms.attributes.backup_theme')</label>
			@error ('backup_theme')
				<div class="invalid-feedback d-block">{{ $message }}</div>
			@enderror
		</div>

		<div class="form-group @error('backup_uploads') is-invalid @enderror">
			<label><input type="radio" name="backup" value="uploads" /> @lang('assistant::archive.forms.attributes.backup_uploads')</label>
			@error ('backup_uploads')
				<div class="invalid-feedback d-block">{{ $message }}</div>
			@enderror
		</div>
	</fieldset>

	<fieldset>
		<legend>@lang('assistant::archive.forms.legends.restore')</legend>

		<div class="form-group @error('restore_uploads') is-invalid @enderror">
			<label><input type="radio" name="restore" value="complex" @checked /> @lang('assistant::archive.forms.attributes.restore_complex')</label>
			@error ('restore_uploads')
				<div class="invalid-feedback d-block">{{ $message }}</div>
			@enderror
		</div>

		<div class="form-group @error('restore_database') is-invalid @enderror">
			<label><input type="radio" name="restore" value="database" /> @lang('assistant::archive.forms.attributes.restore_database')</label>
			@error ('restore_database')
				<div class="invalid-feedback d-block">{{ $message }}</div>
			@enderror
		</div>

		<div class="form-group @error('restore_system') is-invalid @enderror">
			<label><input type="radio" name="restore" value="system" /> @lang('assistant::archive.forms.attributes.restore_system')</label>
			@error ('restore_system')
				<div class="invalid-feedback d-block">{{ $message }}</div>
			@enderror
		</div>

		<div class="form-group @error('restore_theme') is-invalid @enderror">
			<label><input type="radio" name="restore" value="theme" /> @lang('assistant::archive.forms.attributes.restore_theme')</label>
			@error ('restore_theme')
				<div class="invalid-feedback d-block">{{ $message }}</div>
			@enderror
		</div>

		<div class="form-group @error('restore_uploads') is-invalid @enderror">
			<label><input type="radio" name="restore" value="uploads" /> @lang('assistant::archive.forms.attributes.restore_uploads')</label>
			@error ('restore_uploads')
				<div class="invalid-feedback d-block">{{ $message }}</div>
			@enderror
		</div>
	</fieldset>
@endsection
