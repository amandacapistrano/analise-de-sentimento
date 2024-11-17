@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="text-center">
    <h1 class="mb-4">Bem-vindo à Análise de Sentimentos!</h1>
    <p>Para experimentar basta digitar o que você sente ou pensa e nossa ferramenta, baseada em inteligência artificial, retornará uma análise precisa sobre o tom do seu texto.</p>

    @guest
         <p>Nossa ferramenta permite que você envie um texto e descubra rapidamente o sentimento predominante: positivo, negativo ou neutro. Se você não estiver logado, poderá realizar até 10 análises gratuitas. Para usuários registrados, não há limite, e você também pode salvar seu histórico de análises para futuras referências.</p>
        <p class="lead">
            Realize análises de sentimento com texto em português ou outro idioma. 
            Análises diponíveis: <strong>{{ $remainingAnalyses }}</strong>.
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
            <p><strong>Texto analisado:</strong> {{ session('text') }}</p>
            <p><strong>Sentimento: </strong>{{ session('analysis')['label'] }}</p>
            <p><strong>Mensagem: </strong>{{ session('analysis')['message'] }}</p> <!-- Exibe a mensagem do sentimento -->
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
    
  <!-- Legenda de Sentimentos com borda e alinhamento à esquerda -->
  <div class="mt-5 border p-3 text-start">
        <h4>Legenda de Sentimentos</h4>
        <ul class="list-unstyled">
            <li><strong>5 estrelas</strong>: <em>Muito positivo!</em> Seu texto reflete um sentimento extremamente positivo e otimista.</li>
            <li><strong>4 estrelas</strong>: <em>Positivo</em> O sentimento é majoritariamente positivo, com um tom agradável e encorajador.</li>
            <li><strong>3 estrelas</strong>: <em>Neutro</em> O texto transmite um sentimento equilibrado, sem sinais claros de positividade ou negatividade.</li>
            <li><strong>2 estrelas</strong>: <em>Negativo</em> O texto apresenta um sentimento mais negativo, podendo indicar insatisfação ou descontentamento.</li>
            <li><strong>1 estrela</strong>: <em>Muito negativo</em> O sentimento é altamente negativo, expressando insatisfação profunda ou frustração.</li>
        </ul>
    </div>

   
</div>

<!-- Ajuste do espaçamento inferior -->
<style>
    .text-center {
        padding-bottom: 80px; /* Espaçamento para evitar sobreposição com o footer */
    }
</style>

@endsection
