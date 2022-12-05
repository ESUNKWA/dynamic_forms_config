<?php
namespace App\Http\Traits;

use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Http\Request;
/**
 *
 */
trait Logout
{
    public function decnx(Request $request){
        try {

            // Get bearer token from the request
            $accessToken = $request->bearerToken();

            // Get access token from database
            $token = PersonalAccessToken::findToken($accessToken);

            // Revoke token
            $token->delete();

            return  $this->crypt($this->responseSuccess('vous êtes déconnecté'));

        } catch (\Throwable $e) {

            return $this->crypt($this->responseCatchError($e->getMessage()));

        }
    }
}
