<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class NLPController extends Controller
{
    public function detectSarcasmAndOffensive(Request $request)
    {
        $text = $request->input('text');

        if (!$text) {
            return response()->json(['error' => 'Text is required'], 400);
        }

        try {
            $client = new Client(['verify' => false, 'timeout' => 10]);
            $apiKey = config('app.huggingface_api_key');

            if (!$apiKey) {
                return response()->json(['error' => 'Missing Hugging Face API key'], 500);
            }

            // ----------------- 【讽刺检测】 -----------------
            $sarcasmResponse = $client->post('https://api-inference.huggingface.co/models/cardiffnlp/twitter-roberta-base-sentiment', [
                'headers' => [
                    'Authorization' => 'Bearer ' . config('app.huggingface_api_key'), //从 Laravel 配置文件获取 Hugging Face API 密钥
                    'Content-Type'  => 'application/json', // 🔹 设置请求的内容类型为 JSON
                ],
                'json' => ['inputs' => $text],   // 🔹 发送要分析的文本数据
                'http_errors' => false // 🔹 避免 HTTP 请求错误导致异常抛出
            ]);

            $sarcasmStatus = $sarcasmResponse->getStatusCode();
            $sarcasmResult = json_decode($sarcasmResponse->getBody(), true);

            Log::info('Sarcasm API Response:', ['status' => $sarcasmStatus, 'result' => $sarcasmResult]);

            if ($sarcasmStatus !== 200 || empty($sarcasmResult) || !isset($sarcasmResult[0]) || !is_array($sarcasmResult[0])) {
                return response()->json(['error' => 'Sarcasm Model API error', 'details' => $sarcasmResult], $sarcasmStatus);
            }

            $labelMapping = [
                'LABEL_0' => 'Positive',
                'LABEL_1' => 'Neutral',
                'LABEL_2' => 'Negative'
            ];

            $negative_score = 0;
            $neutral_score = 0;
            $positive_score = 0;

            foreach ($sarcasmResult[0] as $item) {
                if ($item['label'] === 'LABEL_2') $negative_score = $item['score'];
                if ($item['label'] === 'LABEL_1') $neutral_score = $item['score'];
                if ($item['label'] === 'LABEL_0') $positive_score = $item['score'];
            }

            // **讽刺逻辑保持不变**
            $sarcasm_keywords = [
                'oh sure', 'seriously?', 'yeah right', 'as if', 'totally', 'you must be kidding', 
                'are you getting paid', 'this is the best', 'wow, just wow', 'unbelievable',
                'stop pretending', 'doesn’t mean it’s special', 'doesn’t mean it’s great', 
                'just because', 'not even special', 'how original', 'what a surprise', 
                'totally legit', 'marketing fluff', 'totally not fake', 'who would believe this'
            ];

            $is_sarcastic = false;

            if ($positive_score > 0.3 && $negative_score > 0.8) {
                $is_sarcastic = false;
            } elseif ($negative_score > 0.8) {
                $is_sarcastic = true;
            } elseif ($negative_score > 0.6 && $neutral_score > 0.2) {
                $is_sarcastic = true;
            } elseif ($positive_score > 0.4 && $neutral_score > 0.3) { 
                foreach ($sarcasm_keywords as $word) {
                    if (stripos($text, $word) !== false) {
                        $is_sarcastic = true;
                        break;
                    }
                }
            }

            $sarcasm_predictions = array_map(function ($item) use ($labelMapping) {
                return [
                    'label' => $labelMapping[$item['label']] ?? $item['label'],
                    'score' => $item['score']
                ];
            }, $sarcasmResult[0]);

            // ----------------- 【不当言论检测】 -----------------
            $offensiveResponse = $client->post('https://api-inference.huggingface.co/models/cardiffnlp/twitter-roberta-base-offensive', [
                'headers' => [
                    'Authorization' => 'Bearer ' . config('app.huggingface_api_key'),
                    'Content-Type'  => 'application/json',
                ],
                'json' => ['inputs' => $text],
                'http_errors' => false
            ]);

            $offensiveStatus = $offensiveResponse->getStatusCode();
            $offensiveResult = json_decode($offensiveResponse->getBody(), true);

            Log::info('Offensive API Response:', ['status' => $offensiveStatus, 'result' => $offensiveResult]);

            if ($offensiveStatus !== 200 || empty($offensiveResult) || !isset($offensiveResult[0]) || !is_array($offensiveResult[0])) {
                return response()->json(['error' => 'Offensive Language Model API error', 'details' => $offensiveResult], $offensiveStatus);
            }

            $offensiveMapping = [
                'LABEL_0' => 'Not Offensive',
                'LABEL_1' => 'Offensive'
            ];

            $is_offensive = false;
            $offensive_score = 0;

            foreach ($offensiveResult[0] as $item) {
                if ($item['label'] === 'LABEL_1') {
                    $offensive_score = $item['score'];
                }
            }

            // **重新加入 offensive_keywords 逻辑**
            $offensive_keywords = [
                'fuck', 'shit', 'bitch', 'asshole', 'bastard', 'dumbass', 'moron', 'stupid', 'idiot', 'retard',
                'i hate you', 'go to hell', 'kill yourself', 'screw you', 'pathetic', 'loser', 'dumb', 'garbage',
                'brain-dead', 'trash', 'disgusting', 'nonsense', 'what a joke', 'clown'
            ];

            // **降低 offensive_score 阈值，并增加关键词检测**
            if ($offensive_score > 0.5) {
                $is_offensive = true;
            }

            foreach ($offensive_keywords as $word) {
                if (stripos($text, $word) !== false) {
                    $is_offensive = true;
                    break;
                }
            }

            $offensive_predictions = array_map(function ($item) use ($offensiveMapping) {
                return [
                    'label' => $offensiveMapping[$item['label']] ?? $item['label'],
                    'score' => $item['score']
                ];
            }, $offensiveResult[0]);

            return response()->json([
                'text' => $text,
                'is_sarcastic' => $is_sarcastic ? 'Yes' : 'No',
                'sarcasm_prediction' => $sarcasm_predictions,
                'is_offensive' => $is_offensive ? 'Yes' : 'No',
                'offensive_prediction' => $offensive_predictions
            ]);
        } catch (RequestException $e) {
            return response()->json(['error' => 'Request failed', 'message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error', 'message' => $e->getMessage()], 500);
        }
    }
}
