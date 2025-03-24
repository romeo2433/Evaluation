@extends('layouts.master')
@section('heading')
    {{ __('Data Import') }}
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <h4>{{ __('Import Data from File') }}</h4>
        <form action="{{ route('data.import-data') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label>{{ __('Select file type') }}:</label>
                <select name="import_type" class="form-control">
                    <option value="csv">CSV</option>
                    <option value="excel">Excel</option>
                    <option value="json">JSON</option>
                </select>
            </div>
            <div class="form-group">
                <label>{{ __('Choose file') }}:</label>
                <input type="file" name="import_file" class="form-control-file">
            </div>
            <button type="submit" class="btn btn-primary">{{ __('Import Data') }}</button>
        </form>
    </div>
</div>
@stop
