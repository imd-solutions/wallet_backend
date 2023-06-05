<?php

namespace Tests;

trait Fragments
{
    /**
     * Function Case: User auth GraphQL fragment.
     * @return array
     */
    public function authFragment()
    {
        return [
            'access_token',
            'user' => $this->userFragment()
        ];
    }

    /**
     * Function Case: User GraphQL fragment.
     * @return array
     */
    public function userFragment()
    {
        return [
            'id',
            'name',
            'email',
            'profile' => $this->profileFragment()
        ];
    }

    /**
     * Function Case: Profile GraphQL fragment.
     * @return array
     */
    public function profileFragment()
    {
        return [
            'firstname',
            'lastname'
        ];
    }

    /**
     * Function Case: Message GraphQL fragment.
     * @return array
     */
    public function messageFragment()
    {
        return [
            'status',
            'message'
        ];
    }

    /**
     * Function Case: Workshop GraphQL fragment.
     * @return array
     */
    public function workshopFragment()
    {
        return [
          'title',
          'slug',
          'short_description',
          'description',
          'start_date',
          'end_date',
          'start_time',
          'end_time',
          'type',
          'price',
          'image',
          'numbers'
        ];
    }

    /**
     * Function Case: Booking GraphQL fragment.
     * @return array
     */
    public function bookingFragment()
    {
        return [
          'firstname',
          'lastname',
          'email',
          'phone',
          'paid',
          'confirmed',
        ];
    }

    /**
     * Function Case: OTP GraphQL fragment.
     * @return array
     */
    public function otpFragment()
    {
        return [
            'access_token',
            'uid',
            'verified',
            'status',
            'message',
            'user' => $this->userFragment()
        ];
    }

    /**
     * Function Case: Atlas GraphQL fragment.
     * @return array
     */
    public function atlasFragment()
    {
        return [
            '... on AtlasSuccessResponse' => $this->atlasSuccessFragment(),
            '... on AtlasErrorResponse' => $this->atlasErrorFragment(),
        ];
    }

    /**
     * Function Case: Atlas Error GraphQL fragment.
     * @return array
     */
    public function atlasErrorFragment()
    {
        return [
            'type',
            'message',
        ];
    }

    /**
     * Function Case: Atlas Login GraphQL fragment.
     * @return array
     */
    public function atlasLoginResponseFragment()
    {
        return [
            'success',
            'token',
        ];
    }

    /**
     * Function Case: Atlas Form GraphQL fragment.
     * @return array
     */
    public function atlasFormResponseFragment()
    {
        return [
            'status',
            'html',
        ];
    }

    /**
     * Function Case: Atlas Success GraphQL fragment.
     * @return array
     */
    public function atlasSuccessFragment()
    {
        return [
            'providers' => [
                'id',
                'name',
                'isActive',
                'currency',
            ]
        ];
    }

    /**
     * Function Case: Atlas Success GraphQL fragment.
     * @return array
     */
    public function userBalanceFragment()
    {
        return [
            'balance'
        ];
    }
}
