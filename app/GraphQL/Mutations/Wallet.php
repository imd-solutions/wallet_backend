<?php

namespace App\GraphQL\Mutations;

use App\Models\Transaction;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Helpers\AtlasAPIHelper;

class Wallet
{
     /**
     * Function Case: User top up wallet.
     *
     * @param null $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param mixed[] $args The arguments that were passed into the field.
     * @param \Nuwave\Lighthouse\Support\Contracts\GraphQLContext $context Arbitrary data that is shared between all fields of a single query.
     * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */

    public function login($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            $formData = [
                'form_params' => [
                    'email' => $args['input']['email'],
                    'password' => $args['input']['password'],
                    'transaction_id' => $args['input']['transaction_id'],
                ]
            ];
            $postData = AtlasAPIHelper::PostApi('login', $formData);

            $postResponse = json_decode($postData->getBody()->getContents());

            return [
                'success' => $postResponse->success,
                'token' => $postResponse->token,
            ];
        }
        catch(\Exception $e) {
            return [
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Function Case: User top up wallet.
     *
     * @param null $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param mixed[] $args The arguments that were passed into the field.
     * @param \Nuwave\Lighthouse\Support\Contracts\GraphQLContext $context Arbitrary data that is shared between all fields of a single query.
     * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */

    public function paymentForm($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = Auth::user()->id;
        $token = $args['input']['token'];
        $amount = (int)$args['input']['amount'];
        $transaction_id = $args['input']['transaction_id'];

        try {
            $formData = [
                'form_params' => [
                      "token" => $token,
                      "country_id" =>  566,
                      "customer_id" =>  $args['input']['customer_id'],
                      "language" =>$args['input']['language'],
                      "amount" =>  $amount,
                      "transaction_id" => $transaction_id,
                      "logo_url" => config('app.frontend_url')."/images/KamaPay_Black.png",
                      "background_colour" => "red",
                      "callback_url" => config('app.frontend_url')."/callback",
                      "error_url" => config('app.frontend_url')."/topUp/error",
                      "success_url" => config('app.frontend_url')."/topUp/success",
                      "extra_properties" => [
                            "bonus" => "1",
                            "bonus_text" => "100% up to 10000 XAF"
                      ]
                ]
            ];

            Transaction::create([
                'user_id' => $user,
                'amount' => $amount,
                'payment_method' => '',
                'status' => 'processing',
                'reference' => $transaction_id,
            ]);

            //ToDO: Update the user balance
            // After the amount has been approved and successful

            $postData = AtlasAPIHelper::PostApi('new-payment',$formData);

            $postResponse = $postData->getBody()->getContents();

            return [
                'status' => 200,
                'html' => str_replace(array("\r", "\n", "\\"), '', htmlspecialchars($postResponse))
            ];

        }
        catch(\Exception $e) {
            return [
                'status' => $e->getCode(),
                'html' => $e->getMessage()
            ];
        }
    }

    /**
     * Function Case: User top up wallet.
     *
     * @param null $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param mixed[] $args The arguments that were passed into the field.
     * @param \Nuwave\Lighthouse\Support\Contracts\GraphQLContext $context Arbitrary data that is shared between all fields of a single query.
     * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */

    public function completeTopUp($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            $trans = Transaction::where(['reference' => $args['input']['transaction_id']])->first();

            if(!$trans) {
                return [
                    "status" => 400,
                    "message" => "That transaction could not be found."
                ];
            }

            $trans->status = $args['input']['status'];
            $trans->payment_method = $args['input']['payment_method'];
            $trans->save();

            $user = User::find($trans->user_id);

            $user->profile->balance = $user->profile->balance + $args['input']['amount'];
            $user->save();

            return [
                "status" => 200,
                "message" => "That has been updated."
            ];
        }
        catch(\Exception $e) {
            return [
                "status" => 500,
                "message" => $e->getMessage()
            ];
        }


    }
}
