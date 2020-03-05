<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\User;
use App\Mail\EmailVerificationLink;
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
        // Check if User exists
        if (!empty($args['id'])) {
            $user = User::find($args['id']);
        }

        // Update if selected user are valid requester
        if (!empty($user)) {
            unset($args['id']);
            unset($args['email']);
            unset($args['password']);
            
            $user->update($args);

            return $user;
        }

        // Create user with ORM
        unset($args['id']);
        unset($args['directive']);

        $user = User::firstOrCreate($args);

        // Send email to user's email address
        try {
            Mail::to($user->email)->send(new EmailVerificationLink($user));
        } catch (\Throwable $th) {
            \Log::error($th);
        }

        // Return user object
        return $user;
    }
}
