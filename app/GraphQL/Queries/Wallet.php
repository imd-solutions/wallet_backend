<?php

namespace App\GraphQL\Queries;

use App\Models\User as UserModel;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class Wallet
{
    /**
     * Return a value for the field.
     *
     * @param null $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param mixed[] $args The arguments that were passed into the field.
     * @param \Nuwave\Lighthouse\Support\Contracts\GraphQLContext $context Arbitrary data that is shared between all fields of a single query.
     * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */

    public function balance($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            // Get the single user from the ID
            $user = Auth::user();

        return [
            'balance' => $user->profile->balance
        ];
        } catch(\Exception $e) {
            throw new Error($e->getMessage());
        }
    }
    /**
     * Return a value for the field.
     *
     * @param null $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param mixed[] $args The arguments that were passed into the field.
     * @param \Nuwave\Lighthouse\Support\Contracts\GraphQLContext $context Arbitrary data that is shared between all fields of a single query.
     * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */

    public function transactions($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            // Get the single user from the ID.
            $user = Auth::user();

            return $user->transactions;

        } catch(\Exception $e) {
            throw new Error($e->getMessage());
        }
    }
}
