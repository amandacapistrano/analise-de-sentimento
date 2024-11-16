@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="text-center">
    <h1 class="mb-4">Bem-vindo à Análise de Sentimentos!</h1>

    @guest
        <p class="lead">
            Realize análises de sentimento com texto em português ou outro idioma. 
            Usuários não cadastrados podem realizar até <strong>{{ $remainingAnalyses }}</strong> análises.
        </p>
    @endguest

    <form action="{{ route('analyze') }}" method="POST" class="mt-4">
        @csrf
        <div class="mb-3">
            <textarea name="text" class="form-control" rows="4" placeholder="Digite seu texto aqui..." required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Analisar Sentimento</button>
    </form>

    @if(session('analysis'))
        <div class="alert alert-success mt-4">
            <h5>Resultado da Análise:</h5>
            <p><strong>Sentimento: </strong>{{ session('analysis')['label'] }}</p>
            <p><strong>Pontuação: </strong>{{ session('analysis')['score'] }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger mt-4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif
</div>
@endsection
