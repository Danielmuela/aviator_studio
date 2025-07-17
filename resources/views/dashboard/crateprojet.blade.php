@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Créer un nouveau projet</h3>

    <form action="{{ route('dashboard.responsable.projets.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="titre">Titre</label>
            <input type="text" name="titre" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" class="form-control" required></textarea>
        </div>

        <div class="form-group">
            <label for="artiste_id">Artiste</label>
            <select name="artiste_id" class="form-control" required>
                @foreach($artistes as $artiste)
                    <option value="{{ $artiste->id }}">{{ $artiste->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-success">Créer</button>
    </form>
</div>
@endsection
