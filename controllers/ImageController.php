<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;

class ImageController extends Controller
{
    public function actionCover(int $seed): Response
    {
        $url = "https://picsum.photos/seed/book{$seed}/200/300";
        $timeout = 5;
        $context = stream_context_create([
            'http' => [
                'timeout' => $timeout,
                'follow_location' => true,
            ],
            'https' => [
                'timeout' => $timeout,
                'follow_location' => true,
            ],
        ]);

        $content = @file_get_contents($url, false, $context);
        if ($content === false) {
            throw new HttpException(502, 'Не удалось получить изображение обложки.');
        }

        $contentType = 'image/jpeg';
        if (isset($http_response_header) && is_array($http_response_header)) {
            foreach ($http_response_header as $header) {
                if (stripos($header, 'Content-Type:') === 0) {
                    $contentType = trim(substr($header, 13));
                    break;
                }
            }
        }

        $response = Yii::$app->response;
        $response->format = Response::FORMAT_RAW;
        $response->headers->set('Content-Type', $contentType);
        $response->headers->set('Cache-Control', 'public, max-age=86400');
        $response->data = $content;

        return $response;
    }
}
