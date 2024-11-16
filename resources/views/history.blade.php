@extends('layouts.app')

@section('title', 'Histórico de Análises')

@section('content')
<div class="container">
    <h1 class="mb-4">Histórico de Análises</h1>
    
    @if($analyses->isEmpty())
        <p>Você ainda não fez nenhuma análise de sentimento.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Texto Analisado</th>
                    <th>Sentimento</th>
                    <th>Pontuação</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($analyses as $analysis)
                    <tr>
                        <td>{{ $analysis->text }}</td>
                        <td>{{ $analysis->label }}</td>
                        <td>{{ $analysis->score }}</td>
                        <td>{{ $analysis->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
