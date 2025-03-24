@extends('layouts.master')

@section('content')

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="container vh-100 d-flex justify-content-center align-items-center">
        <div class="text-center">
            <!-- Formulaire ou autres éléments pour la réinitialisation -->
            <form action="{{ route('truncate') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger">Supprimer</button>
            </form>
        </div>
    </div>

@endsection