<?php

return [

    /*
    |--------------------------------------------------------------------------
    | User provider (auth) implementation.
    |--------------------------------------------------------------------------
    |
    | By default Laravel generic Illuminate\Auth\Guard.
    |
    | Supported options:
    |  - illuminate
    |  - sentry
    */
    'userprovider' => 'jwt-auth',


    /*
    |--------------------------------------------------------------------------
    | User field to be saved as the author of tracked action.
    |--------------------------------------------------------------------------
    |
    | By default:
    |
    |  - id for illuminate
    |  - login field (email) for sentry
    */
    'userfield'    => 'name',


    /*
    |--------------------------------------------------------------------------
    | Table used for the revisions.
    |--------------------------------------------------------------------------
    */
    'table'        => 'revisions',

];
