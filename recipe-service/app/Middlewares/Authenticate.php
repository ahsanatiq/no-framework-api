<?php
namespace App\Middlewares;

use App\Exceptions\UnauthorizedException;
use Closure;
use Illuminate\Http\Request;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Keychain;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;

class Authenticate
{

    public function handle(Request $request, Closure $next)
    {
        $token = $this->getToken($request);
        $jwtToken = $this->parseToken($token);
        $this->validateToken($jwtToken);

        $request->attributes->add(['tokenInfo' => [
            'id'=>$jwtToken->getClaim('id'),
            'aud'=>$jwtToken->getClaim('aud'),
            'sub'=>$jwtToken->getClaim('sub')
        ]]);
        logger()->info('Request Authenticated.', $request->get('tokenInfo'));

        return $next($request);
    }

    protected function getToken(Request $request)
    {
        if (!$token = $request->bearerToken() ?: $request->input('token', null)) {
            throw new UnauthorizedException('Token not present');
        }
        return $token;
    }

    protected function parseToken($token)
    {
        try {
            $jwtToken = (new Parser())->parse($token);
        } catch (\Throwable $e) {
            throw new UnauthorizedException('Invalid token');
        }
        return $jwtToken;
    }

    protected function validateToken(Token $jwtToken)
    {
        $signer = new Sha256();
        $keychain = new Keychain();

        if (!$jwtToken->verify($signer, $keychain->getPublicKey('file://'.config()->get('app.public_key')))) {
            throw new UnauthorizedException('Invalid token');
        }

        if ($jwtToken->isExpired()) {
            throw new UnauthorizedException('Token expired');
        }
    }
}
