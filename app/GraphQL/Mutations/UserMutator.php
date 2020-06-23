<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Support\Facades\Auth;
use App\Mail\EmailVerificationLink;
use Webpatser\Uuid\Uuid;
use App\User;
use Mail;

class UserMutator
{
    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        // TODO implement the resolver
    }

    public function create($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        // Create user with ORM
        unset($args['directive']);
        $args['email_verification_token'] = Uuid::generate()->string;

        if (isset($args['musicGenres'])) {
            $musicGenres = $args['musicGenres'];
            unset($args['musicGenres']);
        }

        if (isset($args['musicInstruments'])) {
            $musicInstruments = $args['musicInstruments'];
            unset($args['musicInstruments']);
        }

        if (isset($args['musicSkills'])) {
            $musicSkills = $args['musicSkills'];
            unset($args['musicSkills']);
        }

        if (isset($args['links'])) {
            $links = $args['links'];
            unset($args['links']);
        }

        $user = User::firstOrCreate($args);

        isset($musicGenres) && $user->musicGenres()->sync($musicGenres);
        isset($musicInstruments) && $user->musicInstruments()->sync($musicInstruments);
        isset($musicSkills) && $user->musicSkills()->sync($musicSkills);
        isset($links) && $user->links()->createMany($links);

        // Send email to user's email address
        try {
            Mail::to($user->email)->send(new EmailVerificationLink($user));
        } catch (\Exception $e) {
            \Log::error($e);
        }

        // Return user object
        return $user;
    }

    public function verify($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = User::where('email_verification_token', $args['token'])->first();
        $user->email_verified_at = date("Y-m-d H:i:s");
        $user->is_verified = true;
        $user->save();

        return $user;
    }

    public function login($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        if (
            ! Auth::attempt(['email' => $args['email'], 'password' => $args['password']]) &&
            ! Auth::attempt(['email' => $args['email'], 'password' => $args['password']])
        ) {
            return response()->json(['error'=>'Unauthorized'], 401);
        }
            
        $user = Auth::user();
        if (!$user->is_verified) {
            $user['token'] = null;
            return $user;
        }

        $user['token'] = $user->createToken('token')->accessToken;

        return $user;
    }

    public function update($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        if (isset($args['profilePhoto'])) {
            $args['profile_photo'] = 'storage/' . $args['profilePhoto']->store(
                'profile_photo', 'public'
            );
        }

        if (isset($args['coverPhoto'])) {
            $args['cover_photo'] = 'storage/' . $args['coverPhoto']->store(
                'cover_photo', 'public'
            );
        }

        return User::find($args['id'])->update($args);
    }

    public function logout($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        if (Auth::check()) {
            Auth::user()->AuthAccessToken()->delete();
        }
    }
}
