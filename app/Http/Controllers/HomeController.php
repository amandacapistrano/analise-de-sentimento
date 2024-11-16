<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SentimentAnalysisService;
use App\Models\SentimentAnalysis; 

class HomeController extends Controller
{
    protected $sentimentService;

    public function __construct(SentimentAnalysisService $sentimentService)
    {
        $this->sentimentService = $sentimentService;
    }

    public function index()
    {
        // Se o usuário não estiver logado, inicializa o contador de análises
        if (!auth()->check()) {
            $remainingAnalyses = session('remaining_analyses', 10); // Inicializa com 20 se não houver valor na sessão
            session(['remaining_analyses' => $remainingAnalyses]); // Define o valor da sessão
        } else {
            $remainingAnalyses = null; // Para usuários logados, você pode definir outro comportamento
        }

        return view('home', compact('remainingAnalyses'));
    }

    public function analyze(Request $request)
{
    // Define o limite de análises para usuários não logados
    $maxAnalysis = 10;
    $sessionKey = 'analysis_count';
    
    // Se o usuário não estiver logado, conta as análises feitas na sessão
    if (!auth()->check()) {
        $currentCount = session($sessionKey, 0);
        
        // Verifica se o limite de 10 análises foi atingido para usuários não logados
        if ($currentCount >= $maxAnalysis) {
            return redirect()->back()->withErrors('Limite de análises atingido para usuários não cadastrados.');
        }
    }
    
    // Valida o texto enviado para análise
    $request->validate([
        'text' => 'required|string|max:1000',
    ]);
    
    // Chama o serviço de análise de sentimento
    $response = $this->sentimentService->analyze($request->input('text'));
    
    // Se houver erro na resposta da API, mostra o erro
    if (isset($response['error'])) {
        return redirect()->back()->withErrors($response['error']);
    }
    
    // Inicializando variáveis para armazenar o melhor sentimento
    $highestScore = null;
    $bestLabel = null;
    $sentimentMessage = '';
    
    // Extraímos o sentimento com a maior pontuação
    foreach ($response as $sentiment) {
        foreach ($sentiment as $item) {
            // Arredonda a pontuação para 2 casas decimais
            $score = round($item['score'], 2);

            // Verifica se é a maior pontuação
            if (is_null($highestScore) || $score > $highestScore) {
                $highestScore = $score;
                $bestLabel = $item['label'];
            }
        }
    }
    
    // Adiciona a mensagem do sentimento com base na label
    switch ($bestLabel) {
        case '5 stars':
            $sentimentMessage = "Muito positivo!";
            break;
        case '4 stars':
            $sentimentMessage = "Positivo";
            break;
        case '3 stars':
            $sentimentMessage = "Neutro";
            break;
        case '2 stars':
            $sentimentMessage = "Negativo";
            break;
        case '1 star':
            $sentimentMessage = "Muito negativo";
            break;
        default:
            $sentimentMessage = "Análise sem sentimento claro.";
            break;
    }

    // Salva a análise na sessão, mas só para usuários não logados
    if (!auth()->check()) {
        session([$sessionKey => $currentCount + 1]);
    }

    // Salva o histórico de análise no banco de dados (para usuários logados)
    if (auth()->check()) {
        SentimentAnalysis::create([
            'user_id' => auth()->id(),
            'text' => $request->input('text'),
            'label' => $bestLabel,
            'score' => $highestScore,
        ]);
    }
    
    // Atualiza o número de análises restantes se o usuário não estiver logado
    if (!auth()->check()) {
        $remainingAnalyses = session('remaining_analyses', 10) - 1;
        session(['remaining_analyses' => $remainingAnalyses]);
    }

 // Retorna a resposta da análise para ser exibida na tela
return redirect()->back()->with([
    'analysis' => [
        'label' => $bestLabel,
        'score' => $highestScore,
        'message' => $sentimentMessage
    ],
    'text' => $request->input('text')  // Passa o texto de volta
]);

}


    

    public function history()
    {
        $analyses = SentimentAnalysis::where('user_id', auth()->id())->get(); // Busca as análises do usuário logado

        return view('history', compact('analyses'));
    }

}
