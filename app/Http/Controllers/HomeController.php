<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SentimentAnalysisService;

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
            $remainingAnalyses = session('remaining_analyses', 20); // Inicializa com 20 se não houver valor na sessão
            session(['remaining_analyses' => $remainingAnalyses]); // Define o valor da sessão
        } else {
            $remainingAnalyses = null; // Para usuários logados, você pode definir outro comportamento
        }

        return view('home', compact('remainingAnalyses'));
    }

    public function analyze(Request $request)
    {
        $maxAnalysis = 20;
        $sessionKey = 'analysis_count';
        
        // Conta quantas análises o usuário fez na sessão
        $currentCount = session($sessionKey, 0);
        
        // Verifica se o limite de 20 análises foi atingido
        if ($currentCount >= $maxAnalysis) {
            return redirect()->back()->withErrors('Limite de análises atingido para usuários não cadastrados.');
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
        
        // Extraímos o sentimento com a maior pontuação
        $highestScore = null;
        $bestLabel = null;
    
        foreach ($response as $sentiment) {
            foreach ($sentiment as $item) {
                if (is_null($highestScore) || $item['score'] > $highestScore) {
                    $highestScore = $item['score'];
                    $bestLabel = $item['label'];
                }
            }
        }
        
        // Salva a análise na sessão
        session([$sessionKey => $currentCount + 1]);
    
        // Atualiza o número de análises restantes (se o usuário não estiver logado)
        if (!auth()->check()) {
            $remainingAnalyses = session('remaining_analyses', 20) - 1;
            session(['remaining_analyses' => $remainingAnalyses]);
        }
    
        // Retorna a resposta da análise para ser exibida na tela
        return redirect()->back()->with('analysis', ['label' => $bestLabel, 'score' => $highestScore]);
    }
    
}
