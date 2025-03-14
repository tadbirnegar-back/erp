<?php

namespace Modules\ACC\app\Http\Traits;

use Modules\ACC\app\Models\Article;
use Modules\ACC\app\Models\Document;

trait ArticleTrait
{
    public function storeArticle(array $data, Document $document)
    {
        $data = $this->articleDataPreparation($data, $document);
        $article = Article::create($data->toArray()[0]);

        return $article;

    }

    public function bulkStoreArticle(array $data, Document $document)
    {
        $data = $this->articleDataPreparation($data, $document);
        $article = Article::upsert($data->toArray(), ['id']);

        $articles = Article::take($data->count())->orderBy('id', 'desc')->get();

        return $articles;

    }

    public function articleDataPreparation(array $data, Document $document)
    {
        if (!isset($data[0]) || !is_array($data[0])) {
            $data = [$data];
        }

        $data = collect($data)->map(function ($item) use ($document) {
            return [
                'id' => $item['id'] ?? null,
                'description' => convertToDbFriendly($item['description']) ?? null,
                'priority' => $item['priority'] ?? 1,
                'debt_amount' => $item['debtAmount'] ?? 0,
                'credit_amount' => $item['creditAmount'] ?? 0,
                'account_id' => $item['accountID'],
                'document_id' => $document->id,
                'transaction_id' => $item['transactionID'] ?? null,
            ];
        });

        return $data;

    }

}
