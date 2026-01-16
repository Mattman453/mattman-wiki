<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use stdClass;

class Page extends Model
{
    protected $fillable = [
        'sections',
        'game',
        'subtitle',
        'page',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function game() {
        return $this->belongsTo(Game::class, 'game', 'game');
    }

    public static function convertPageBodiesToTypeStyle() {
        $pages = self::get()->toarray();
        foreach ($pages as &$page) {
            foreach ($page['sections'] as $i => &$section) {
                foreach ($section['body'] as $body) {
                    if (isset($body['type'])) {
                        break;
                    }
                    $newBody = new stdClass();
                    $newBody->type = 'text';
                    $newBody->body = $body;
                    $section['body'] = $newBody;
                }
            }
            self::updateOrCreate(
                ['game' => $page['game'], 'subtitle' => $page['subtitle'], 'page' => $page['page']],
                ['sections' => $page['sections']],
            );
        }
    }
}
