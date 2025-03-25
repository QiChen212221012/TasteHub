<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use GuzzleHttp\Client;
use App\Models\Comment;
use Illuminate\Support\Facades\Log;

class ProcessNLPComment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function handle()
    {
        try {
            $client = new Client(['verify' => false, 'timeout' => 5]);

            // 获取 Hugging Face API Key
            $apiKey = config('app.huggingface_api_key');
            if (!$apiKey) {
                Log::error('Missing Hugging Face API key');
                return;
            }

            // ----------------- 【讽刺检测】 -----------------
            $sarcasmResponse = $client->post(env('NLP_SARCASTIC_API_URL'), [
                'headers' => [
                    'Authorization' => "Bearer {$apiKey}",
                    'Content-Type' => 'application/json',
                ],
                'json' => ['inputs' => $this->comment->content],
                'http_errors' => false
            ]);

            $sarcasmResult = json_decode($sarcasmResponse->getBody(), true);
            Log::info('Sarcasm API Response:', ['result' => $sarcasmResult]);

            // 解析讽刺检测结果
            $is_sarcastic = false; // Initialize the variable, assuming the text is NOT sarcastic by default
            if (isset($sarcasmResult[0]) && is_array($sarcasmResult[0])) {//Ensure the API response is valid
                foreach ($sarcasmResult[0] as $item) {//Iterate through the sentiment analysis results
                    if ($item['label'] === 'LABEL_2' && $item['score'] > 0.6) {
                        //If the model predicts "LABEL_2" (typically sarcasm) and confidence score > 0.6
                        $is_sarcastic = true;
                        break;
                    }
                }
            }

            // ----------------- 【不当言论检测】 -----------------
            $offensiveResponse = $client->post(env('NLP_OFFENSIVE_API_URL'), [
                'headers' => [
                    'Authorization' => "Bearer {$apiKey}",
                    'Content-Type' => 'application/json',
                ],
                'json' => ['inputs' => $this->comment->content],
                'http_errors' => false
            ]);

            $offensiveResult = json_decode($offensiveResponse->getBody(), true);
            Log::info('Offensive API Response:', ['result' => $offensiveResult]);

            // 解析不当言论检测结果
            $is_offensive = false;
            if (isset($offensiveResult[0]) && is_array($offensiveResult[0])) {
                foreach ($offensiveResult[0] as $item) {
                    if ($item['label'] === 'LABEL_1' && $item['score'] > 0.5) {
                        $is_offensive = true;
                        break;
                    }
                }
            }

            // ✅ 存储 `type` 以 JSON 数组格式
            $types = [];
            if ($is_sarcastic) {
                $types[] = 'sarcastic';
            }
            if ($is_offensive) {
                $types[] = 'offensive';
            }

            // 🚩 如果有问题，修改 `status` 为 `reported`
            $status = !empty($types) ? 'reported' : 'approved';

            // ✅ 更新评论
            $this->comment->update([
                'status' => $status,
                'type' => json_encode($types, JSON_UNESCAPED_UNICODE), // 存 JSON 数组
            ]);

        } catch (\Exception $e) {
            Log::error('NLP API Request Failed: ' . $e->getMessage());
        }
    }
}
