<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    /**
     * Gets the user associated with the page.
     * 
     * @return BelongsTo the relationship allowing the fetching of the user.
     */
    public function user() : BelongsTo {
        return $this->belongsTo(User::class);
    }

    /**
     * Gets the user associated with the page.
     * 
     * @return BelongsTo the relationship allowing the fetching of the user.
     */
    public function game() : BelongsTo {
        return $this->belongsTo(Game::class, 'game', 'game');
    }

    /**
     * Converts old style text-only bodies with each paragraph separated in array values and converts to new [type => string, data => any].
     * 
     * @depracated No longer used and not recommended
     */
    public static function convertPageBodiesToTypeStyle() {
        $pages = self::get()->toarray();
        foreach ($pages as &$page) {
            foreach ($page['sections'] as $i => &$section) {
                if (isset($section['body']['type'])) {
                    break;
                }
                $newBody = new stdClass();
                $newBody->type = 'text';
                $newBody->data = join('\n\n', $section['body']);
                $section['body'] = $newBody;
            }
            self::updateOrCreate(
                ['game' => $page['game'], 'subtitle' => $page['subtitle'], 'page' => $page['page']],
                ['sections' => $page['sections']],
            );
        }
    }
}
