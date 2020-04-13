<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
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

        $user = User::firstOrCreate($args);

        // Send email to user's email address
        try {
            Mail::to($user->email)->send(new EmailVerificationLink($user));
        } catch (\Exception $e) {
            \Log::error($e);
        }

        // Return user object
        return $user;
    }

    public function verify($rootValue, array $args)
    {
        return User::where('email_verification_token', $args['token'])->update(['email_verified_at' => date(), 'is_verified' => true]);
    }

    public function update($rootValue, array $args)
    {
        return User::find($args['id'])->update($args);
    }
}
