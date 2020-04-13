<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
 
class User extends Authenticatable
{
    use HasApiTokens,Notifiable;
 
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'phone_number',
        'birth_date',
        'address',
        'kecamatan',
        'kabupaten_kota',
        'provinsi',
        'negara',
        'fee',
        'bank',
        'bank_account_number',
        'id_type',
        'id_number',
        'email_verification_token',
        'is_verified',
    ];
 
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
 
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function musicInstruments(): BelongsToMany
    {
      return $this->belongsToMany('App\MusicInstrument', 'user_music_instrument', 'user_id', 'music_instrument_id')->withTimeStamps();
    }

    public function musicGenres(): BelongsToMany
    {
      return $this->belongsToMany('App\MusicGenre', 'user_music_genre', 'user_id', 'music_genre_id')->withTimeStamps();
    }

    public function musicSkills(): BelongsToMany
    {
      return $this->belongsToMany('App\MusicSkill', 'user_music_skill', 'user_id', 'music_skill_id')->withTimeStamps();
    }

    public function links(): HasMany
    {
      return $this->hasMany('App\Link', 'user_id', 'id');
    }
}
