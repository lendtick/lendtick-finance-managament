<?php

namespace App\Http\Controllers;

class VersionsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    /**
     * @SWG\Parameter(parameter="Authorization", name="Authorization", type="string", in="header", required=true)
    */

    /**
     * @SWG\Swagger(
     *     basePath="/",
     *     schemes={"http","https"},
     *     @SWG\Info(
     *         version="1.0.0",
     *         title="User - Lendtick",
     *         @SWG\Contact(
     *             email="faujiakbar@gmail.com"
     *         ),
     *     ),
     *     @SWG\SecurityScheme(
     *         securityDefinition="Bearer",
     *         type="apiKey",
     *         name="Authorization",
     *         in="header"
     *     )
     * )
     */
    public function __construct()
    {
        //
    }

    //
}
