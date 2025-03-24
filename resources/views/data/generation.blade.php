@extends('layouts.master')
@section('heading')
    {{ __('Data Generation') }}
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <h4>{{ __('Generate Sample Data') }}</h4>
        <form action="{{ route('data.generate-data') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>{{ __('Select data to generate') }}:</label>
                <div class="checkbox">
                    <label><input type="checkbox" name="types[]" value="clients"> {{ __('Clients') }}</label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" name="types[]" value="tasks"> {{ __('Tasks') }}</label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" name="types[]" value="leads"> {{ __('Leads') }}</label>
                </div>
            </div>
            <div class="form-group">
                <label>{{ __('Number of records') }}:</label>
                <input type="number" name="record_count" class="form-control" value="10" min="1" max="100">
            </div>
            <button type="submit" class="btn btn-primary">{{ __('Generate Data') }}</button>
        </form>
    </div>
</div>
@stop
