@extends('layouts.master')
@section('heading')
    {{ __('Table Cleaning') }}
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <div class="alert alert-warning">
            <strong>{{ __('Warning!') }}</strong> 
            {{ __('This action will permanently delete data from the selected tables. Protected system tables and administrator accounts will not be affected.') }}
        </div>

        <h4>{{ __('Clean Database Tables') }}</h4>
        <form action="{{ route('data.clean-tables') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>{{ __('Select tables to clean') }}:</label>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" id="select-all"> {{ __('Select All') }}
                    </label>
                </div>
                @foreach($tables as $table)
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="tables[]" value="{{ $table }}" class="table-checkbox"> 
                        {{ __(ucfirst($table)) }}
                    </label>
                </div>
                @endforeach
            </div>
            <button type="submit" class="btn btn-danger" onclick="return confirm('{{ __('Are you sure you want to clean these tables? This action cannot be undone.') }}')">
                {{ __('Clean Selected Tables') }}
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('select-all');
    const tableCheckboxes = document.querySelectorAll('.table-checkbox');

    selectAll.addEventListener('change', function() {
        tableCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    tableCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            selectAll.checked = [...tableCheckboxes].every(cb => cb.checked);
        });
    });
});
</script>
@stop
