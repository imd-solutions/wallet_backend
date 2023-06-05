<?php

namespace App\GraphQL\Mutations;

use App\Models\Profile;
use App\Models\User as UserModel;
use App\Notifications\UserOTPNotification;
use Carbon\Carbon;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class User
{

    public const OTP_MINUTES = 5;
    /**
     * Function Case: Create a user with the input data.
     *
     * @param null $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param mixed[] $args The arguments that were passed into the field.
     * @param \Nuwave\Lighthouse\Support\Contracts\GraphQLContext $context Arbitrary data that is shared between all fields of a single query.
     * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */

    public function create($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        // Create the new users.
        $user = UserModel::create([
            'name' => $args['input']['firstname'] . ' ' . $args['input']['lastname'],
            'email' => $args['input']['email'],
            'password' => isset($args['input']['password']) ? Hash::make($args['input']['password']) : Hash::make('P455w0Rd!')
        ]);

        // Create the users profile.
        Profile::create([
            'user_id' => $user->id,
            'firstname' => $args['input']['firstname'],
            'lastname' => $args['input']['lastname']
        ]);

        $user->save();

        return $user;
    }

    /**
     * Function Case: Resend the user confirmation info.
     *
     * @param null $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param array $args The arguments that were passed into the field.
     * @param GraphQLContext|null $context Arbitrary data that is shared between all fields of a single query.
     * @param ResolveInfo $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     *
     * @return mixed
     */
    public function resend($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            $user = UserModel::where(['email' => $args['email']])->first();

            if (!$user) {
                throw new Error('That user does not exist.');
            }

            return [
                'status' => 200,
                'title' => 'Success',
                'css' => 'is-success',
                'message' => 'That has been re-sent for you.'
            ];


        } catch (\Exception $e) {
            return [
                'status' => 400,
                'title' => 'Error',
                'css' => 'is-danger',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Function Case: Verify the users email.
     *
     * @param null $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param array $args The arguments that were passed into the field.
     * @param GraphQLContext|null $context Arbitrary data that is shared between all fields of a single query.
     * @param ResolveInfo $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     *
     * @return mixed
     */
    public function email($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {

            // Get the users email address.
            $confirm = Crypt::decrypt($args['code']);

            // Find the user with the email code.
            $user = UserModel::where(['email' => $confirm["email"]])->firstOrFail();

            // Check to see if the account has been verified.
            if ($user->email_verified_at) {
                $data = [
                    "css" => "is-warning",
                    "title" => "Warning!",
                    "message" => "Your account has already been verified."
                ];

                return $data;
            }

            // Update the users email verified date.
            $user->email_verified_at = Carbon::now();
            $user->save();

            $data = [
                "css" => "is-success",
                "title" => "Success!",
                "message" => "Your account has been verified. Please log into your account."
            ];

            return $data;


        } catch (DecryptException $exception) {

            $data = [
                "css" => "is-danger",
                "title" => "Error!",
                "message" => "Sorry. That is not a valid link. Please check and try again."
            ];

            return $data;
        }
    }

    /**
     * Function Case: Update the user details.
     *
     * @param null $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param array $args The arguments that were passed into the field.
     * @param GraphQLContext|null $context Arbitrary data that is shared between all fields of a single query.
     * @param ResolveInfo $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     *
     * @return mixed
     */
    public function update($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        // Get the user from the id.
        $user = UserModel::find($args['input']['id']);

        // Update the user details.
        $user->name = $args['input']['firstname'] . ' ' . $args['input']['lastname'];
        $user->email = $args['input']['email'];
        $user->isVerified = true;

        // Update the user profile.
        $user->profile()->update([
            'firstname' => $args['input']['firstname'],
            'lastname' => $args['input']['lastname'],
            "country_id" => $args['input']['country_id'] ?? $args['input']['country_id'],
            "currency_code" => $args['input']['currency_code'] ?? $args['input']['currency_code'],
//            "language_code" => $args['input']['language_code'] ?? $args['input']['language_code'],
        ]);

        // Save the user.
        $user->save();

        $token = $user->createToken('App Access Client')->accessToken;

        Auth::login($user);

        // Return the user object.
        return [
            'access_token' => $token,
            'user' => $user
        ];
    }

    /**
     * Function Case: Create an OTP for the user.
     *
     * @param null $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param array $args The arguments that were passed into the field.
     * @param GraphQLContext|null $context Arbitrary data that is shared between all fields of a single query.
     * @param ResolveInfo $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     *
     * @return mixed
     */
    public function createOTP($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            $phoneNumber =  $args['input']['areaCode'].$args['input']['phone'];

            $user = UserModel::firstOrNew([
                "phone" => $phoneNumber
            ]);

            if(is_null($user->id)) {
                $data = [
                    "firstname" => "OTP",
                    "lastname" => "User",
                    "name" => "OTP User",
                    "phone" => $phoneNumber,
                    "email" => "otp-".date('ymdhis')."@test.com",
                    "password" =>  encrypt(rand(0, 999999))
                ];
                $user = $this->userCreate($data);
            }

            if(config('app.env') === 'local') {
                $otp = 1234;

                $this->cacheOTP($user->id, $otp);
            }
            else {
                $otp = rand(1000, 9999);

                $this->cacheOTP($user->id, $otp);

                Notification::send($user, new UserOTPNotification('otp_sms', $otp, $phoneNumber));
            }
            return [
                'status' => 200,
                'message' => "That has been processed for you."
            ];
        }
        catch(\Exception $e) {
            return [
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }

    }

    /**
     * Function Case:Confirm user OTP.
     *
     * @param null $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param array $args The arguments that were passed into the field.
     * @param GraphQLContext|null $context Arbitrary data that is shared between all fields of a single query.
     * @param ResolveInfo $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     *
     * @return mixed
     */
    public function confirmOTP($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            $user = UserModel::where(['phone' => $args['input']['phone']])->first();

            if (Cache::has($this->otpAuthKey($user->id)) && $args['input']['code'] == Cache::get($this->otpAuthKey($user->id))){

                $token = null;

                if($user->isVerified) {
                    $token = $user->createToken('App Access Client')->accessToken;
                }

                Auth::login($user, true);

                return [
                    'access_token' => $token,
                    'uid' => $user->id,
                    'verified' => $user->isVerified,
                    'status' => 200,
                    'message' => "That OTP has been authorised."
                ];
            } else {
                throw new \Exception('That OTP code is either invalid or has expired.', 422);
            }
        }
        catch(\Exception $e) {
            return [
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }

    }

    /**
     * Function Case: Update the user details.
     *
     * @param null $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param array $args The arguments that were passed into the field.
     * @param GraphQLContext|null $context Arbitrary data that is shared between all fields of a single query.
     * @param ResolveInfo $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     *
     * @return mixed
     */
    public function socialLogin($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = UserModel::firstOrNew([
            "provider_id" => $args['input']['provider_id']
        ]);

        if(is_null($user->id)) {
            $data = [
                "firstname" => $args['input']['firstname'],
                "lastname" => $args['input']['lastname'],
                "name" => $args['input']['firstname'] .' ' .$args['input']['lastname'],
                "email" => $args['input']['email'],
                "password" => encrypt(rand(111111, 999999)),
                "provider_id" => $args['input']['provider_id'],
                "isVerified" => true,
            ];

            $user = $this->userCreate($data);
        }

        $token = $user->createToken('App Access Client')->accessToken;

        // Return the user object.
        return [
            'access_token' => $token,
            'user' => $user
        ];
    }

    private function otpAuthKey($uid)
    {
        return "OTP_auth_{$uid}";
    }

    private function userCreate($data)
    {
        $user = UserModel::firstOrNew([
            "email" => $data['email']
        ]);

        if(is_null($user->id)) {

            $user = UserModel::create($data);

            unset($data['password']);

            $user->profile()->create($data);

            return $user;
        }

        $user->isVerified = true;
        $user->provider_id = $data['provider_id'];
        $user->save();

        return $user;
    }

    private function cacheOTP($uid, $otp) {
        Cache::put($this->otpAuthKey($uid), $otp, now()->addMinutes(User::OTP_MINUTES));
    }
}
