<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class RTPGame extends Model
{
    use HasFactory, HasUuids;
    protected $table = 'rtp_games';

    protected $fillable = [
        'provider_id',
        'name',
        'rtp',
        'pola',
        'rating',
        'img_src',
        'step_one',
        'type_step_one',
        'desc_step_one',
        'step_two',
        'type_step_two',
        'desc_step_two',
        'step_three',
        'type_step_three',
        'desc_step_three',
        'step_four',
        'type_step_four',
        'desc_step_four',
        'stake_bet',
        'last_rtp_update',
    ];

    protected $casts = [
        'rtp' => 'integer',
        'pola' => 'integer',
        'rating' => 'integer',
        'step_one' => 'integer',
        'step_two' => 'integer',
        'step_three' => 'integer',
        'step_four' => 'integer',
        'stake_bet' => 'integer',
        'last_rtp_update' => 'datetime',
    ];

    /**
     * Get the provider that owns this game
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }


    /**
     * Get effectiveness data (deprecated - effectiveness field removed)
     */
    public function getEffectivenessData(): array
    {
        return [
            'boost_effectiveness' => 0,
            'max_boost_limit' => 0,
            'progress_loss_on_refresh' => false,
            'category' => 'unknown'
        ];
    }

    /**
     * Get RTP configuration settings
     *
     * @return array
     */
    public function getRTPConfiguration(): array
    {
        return [
            'rtp' => $this->rtp ?? 0,
            'pola' => $this->pola ?? 50,
        ];
    }

    /**
     * Set RTP configuration settings
     *
     * @param array $config
     * @return bool
     */
    public function setRTPConfiguration(array $config): bool
    {
        try {
            $this->update([
                'rtp' => $config['rtp'] ?? 0,
                'pola' => $config['pola'] ?? 50,
            ]);
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to update RTP configuration for game ' . $this->name . ': ' . $e->getMessage());
            return false;
        }
    }

    public static function randomRTPGameData($providerId)
    {
        // Get provider config for min/max RTP and pola values
        $provider = Provider::find($providerId);
        
        if (!$provider) {
            $min_rtp = 50;
            $max_rtp = 95;
            $min_pola = 50;
            $max_pola = 95;
        } else {
            $min_rtp = $provider->min_rtp ?? 50;
            $max_rtp = $provider->max_rtp ?? 95;
            $min_pola = $provider->min_pola ?? 50;
            $max_pola = $provider->max_pola ?? 95;
        }

        $randomRTP = rand($min_rtp, $max_rtp);
        $typeStepOptions = ['Pancing Freespin', 'Naikkan Taruhan'];
        $descStepOptions = ['Spin Normal Untuk Memancing Freespin.', 'Dengan Turbo Off.', 'Spin Normal (Double Chance On).'];
        $randomStakeBet = rand(400, 15000);

        // Generate different random values for each step
        $stepOne = rand($min_pola, $max_pola);
        $stepTwo = rand($min_pola, $max_pola);
        $stepThree = rand($min_pola, $max_pola);
        $stepFour = rand($min_pola, $max_pola);

        $randomPola = rand($min_pola, $max_pola);
        $rating = rand(1, 5);

        return [
            'rtp' => $randomRTP,
            'pola' => $randomPola,
            'step_one' => $stepOne,
            'step_two' => $stepTwo,
            'step_three' => $stepThree,
            'step_four' => $stepFour,
            'type_step_one' => $typeStepOptions[rand(0, count($typeStepOptions) - 1)],
            'desc_step_one' => $descStepOptions[rand(0, count($descStepOptions) - 1)],
            'type_step_two' => $typeStepOptions[rand(0, count($typeStepOptions) - 1)],
            'desc_step_two' => $descStepOptions[rand(0, count($descStepOptions) - 1)],
            'type_step_three' => $typeStepOptions[rand(0, count($typeStepOptions) - 1)],
            'desc_step_three' => $descStepOptions[rand(0, count($descStepOptions) - 1)],
            'type_step_four' => $typeStepOptions[rand(0, count($typeStepOptions) - 1)],
            'desc_step_four' => $descStepOptions[rand(0, count($descStepOptions) - 1)],
            'stake_bet' => $randomStakeBet,
            'rating' => $rating,
        ];
    }
}
