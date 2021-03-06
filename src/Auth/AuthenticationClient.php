<?php


namespace Authing\Auth;

use InvalidArgumentException;
use Authing\Types\BindPhoneParam;
use Authing\Types\CheckLoginStatusParam;
use Authing\Types\CommonMessage;
use Authing\Types\EmailScene;
use Authing\Types\JWTTokenStatus;
use Authing\Types\LoginByEmailInput;
use Authing\Types\LoginByEmailParam;
use Authing\Types\LoginByPhoneCodeInput;
use Authing\Types\LoginByPhoneCodeParam;
use Authing\Types\LoginByPhonePasswordInput;
use Authing\Types\LoginByPhonePasswordParam;
use Authing\Types\LoginByUsernameInput;
use Authing\Types\LoginByUsernameParam;
use Authing\Types\RefreshToken;
use Authing\Types\RefreshTokenParam;
use Authing\Types\RegisterByEmailInput;
use Authing\Types\RegisterByEmailParam;
use Authing\Types\RegisterByPhoneCodeInput;
use Authing\Types\RegisterByPhoneCodeParam;
use Authing\Types\RegisterByUsernameInput;
use Authing\Types\RegisterByUsernameParam;
use Authing\Types\RemoveUdvParam;
use Authing\Types\ResetPasswordParam;
use Authing\Types\SendEmailParam;
use Authing\Types\SetUdvParam;
use Authing\Types\UDFTargetType;
use Authing\Types\UdvParam;
use Authing\Types\UnbindPhoneParam;
use Authing\Types\UpdateEmailParam;
use Authing\Types\UpdatePasswordParam;
use Authing\Types\UpdatePhoneParam;
use Authing\Types\UpdateUserInput;
use Authing\Types\UpdateUserParam;
use Authing\Types\User;
use Authing\Types\UserDefinedData;
use Authing\Types\UserParam;
use Authing\BaseClient;
use Authing\Types\CheckPasswordStrengthParam;
use Authing\Types\ListUserAuthorizedResourcesParam;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use Error;
use Exception;
use stdClass;

function formatAuthorizedResources($obj) {
    $authorizedResources = $obj->authorizedResources;
    $list = $authorizedResources->list;
    $total = $authorizedResources->totalCount;
    array_map(function($_){
        foreach($_ as $key => $value) {
            if(!$_->$key) {
                unset($_->$key);
            }
        }
        return $_;
    }, (array)$list);
    $res = new stdClass;
    $res->list = $list;
    $res->totalCount = $total;
    return $res;
}


class AuthenticationClient extends BaseClient
{

    protected $user;

    public $PasswordSecurityLevel = array("LOW" => 1, "MIDDLE" => 2, "HIGH" => 3);
    /**
     * AuthenticationClient constructor.
     * @param $userPoolId string
     * @throws InvalidArgumentException
     */
    public function __construct($userPoolIdOrFunc)
    {
        parent::__construct($userPoolIdOrFunc);
    }

    public function setMfaAuthorizationHeader(string $token)
    {
        $this->mfaToken = $token;
    }

    public function clearMfaAuthorizationHeader()
    {
        $this->mfaToken = "";
    }

    /**
     * ??????????????????
     *
     * @return User
     * @throws Exception
     */
    function getCurrentUser()
    {
        $param = new UserParam();
        $user = $this->request($param->createRequest());
        $this->accessToken = $user->token ?: $this->accessToken;
        $this->user = $user;
        return $user;
    }

    /**
     * ??????????????????
     *
     * @return User
     * @throws Exception
     */
    function setCurrentUser()
    {
        $param = new UserParam();
        $user = $this->request($param->createRequest());
        $this->accessToken = $user->token ?: $this->accessToken;
        $this->user = $user;
        return $user;
    }


    /**
     * ????????????????????????
     *
     * @param $input RegisterByEmailInput
     * @return User
     * @throws Exception
     */
    function registerByEmail($input)
    {
        if ($input->password) {
            $input->password = $this->encrypt($input->password);
        }

        $param = new RegisterByEmailParam($input);
        $user = $this->request($param->createRequest());
        $this->accessToken = $user->token ?: $this->accessToken;
        return $user;
    }

    /**
     * ???????????????????????????
     *
     * @param $input RegisterByUsernameInput
     * @return User
     * @throws Exception
     */
    function registerByUsername($input)
    {
        if ($input->password) {
            $input->password = $this->encrypt($input->password);
        }

        $param = new RegisterByUsernameParam($input);
        $user = $this->request($param->createRequest());
        $this->accessToken = $user->token ?: $this->accessToken;
        return $user;
    }

    /**
     * ??????????????????????????????
     *
     * @param $input RegisterByPhoneCodeInput
     * @return User
     * @throws Exception
     */
    function registerByPhoneCode($input)
    {
        if ($input->password) {
            $input->password = $this->encrypt($input->password);
        }

        $param = new RegisterByPhoneCodeParam($input);
        $user = $this->request($param->createRequest());
        $this->accessToken = $user->token ?: $this->accessToken;
        return $user;
    }

    /**
     * ?????????????????????
     *
     * @param $phone
     * @return object
     * @throws Exception
     */
    function sendSmsCode($phone)
    {
        return $this->httpPost("/api/v2/sms/send", [
            "phone" => $phone
        ]);
    }

    /**
     * ??????????????????
     *
     * @param $input LoginByEmailInput
     * @return User
     * @throws Exception
     */
    function loginByEmail($input)
    {
        if ($input->password) {
            $input->password = $this->encrypt($input->password);
        }

        $param = new LoginByEmailParam($input);
        $user = $this->request($param->createRequest());
        $this->accessToken = $user->token ?: $this->accessToken;
        return $user;
    }

    /**
     * ???????????????????????????
     *
     * @param $input LoginByUsernameInput
     * @return User
     * @throws Exception
     */
    function loginByUsername($input)
    {
        if ($input->password) {
            $input->password = $this->encrypt($input->password);
        }

        $param = new LoginByUsernameParam($input);
        $user = $this->request($param->createRequest());
        $this->accessToken = $user->token ?: $this->accessToken;
        return $user;
    }

    /**
     * ??????????????????????????????
     *
     * @param $input LoginByPhoneCodeInput
     * @return User
     * @throws Exception
     */
    function loginByPhoneCode($input)
    {
        $param = new LoginByPhoneCodeParam($input);
        $user = $this->request($param->createRequest());
        $this->accessToken = $user->token ?: $this->accessToken;
        return $user;
    }

    /**
     * ???????????????????????????
     *
     * @param $input LoginByPhonePasswordInput
     * @return User
     * @throws Exception
     */
    function loginByPhonePassword($input)
    {
        if ($input->password) {
            $input->password = $this->encrypt($input->password);
        }

        $param = new LoginByPhonePasswordParam($input);
        $user = $this->request($param->createRequest());
        $this->accessToken = $user->token ?: $this->accessToken;
        return $user;
    }

    /**
     * ??????????????????
     *
     * @param string $token
     * @return JWTTokenStatus
     * @throws Exception
     */
    function checkLoginStatus($token = null)
    {
        $param = (new CheckLoginStatusParam())->withToken($token);
        return $this->request($param->createRequest());
    }

    /**
     * ????????????
     *
     * @param $email string ??????
     * @param $scene EmailScene ????????????
     * @return CommonMessage
     * @throws Exception
     */
    function sendEmail($email, $scene)
    {
        $param = new SendEmailParam($email, $scene);
        return $this->request($param->createRequest());
    }

    /**
     * ????????????????????????????????????
     *
     * @param $phone string ?????????
     * @param $code string ??????????????????
     * @param $newPassword string ?????????
     * @return CommonMessage
     * @throws Exception
     */
    function resetPasswordByPhoneCode($phone,  $code, $newPassword)
    {
        $newPassword = $this->encrypt($newPassword);
        $param = (new ResetPasswordParam($code, $newPassword))->withPhone($phone);
        return $this->request($param->createRequest());
    }

    /**
     * ?????????????????????????????????
     *
     * @param $email string ??????
     * @param $code string ???????????????
     * @param $newPassword string ?????????
     * @return CommonMessage
     * @throws Exception
     */
    function resetPasswordByEmailCode($email, $code, $newPassword)
    {
        $newPassword = $this->encrypt($newPassword);
        $param = (new ResetPasswordParam($code, $newPassword))->withEmail($email);
        return $this->request($param->createRequest());
    }

    /**
     * ??????????????????
     *
     * @param $input UpdateUserInput
     * @return User
     * @throws Exception
     */
    function updateProfile($input)
    {
        $param = new UpdateUserParam($input);
        $user = $this->request($param->createRequest());
        $this->accessToken = $user->token ?: $this->accessToken;
        return $user;
    }

    /**
     * ????????????
     *
     * @param $newPassword string ?????????
     * @param $oldPassword string ?????????
     * @return User
     * @throws Exception
     */
    function updatePassword($newPassword, $oldPassword)
    {
        $newPassword = $this->encrypt($newPassword);
        $oldPassword = $this->encrypt($oldPassword);
        $param = (new UpdatePasswordParam($newPassword))->withOldPassword($oldPassword);
        $user = $this->request($param->createRequest());
        $this->accessToken = $user->token ?: $this->accessToken;
        return $user;
    }

    /**
     * ???????????????
     *
     * @param $phone string ?????????
     * @param $phoneCode string ??????????????????
     * @param $oldPhone string ????????????
     * @param $oldPhoneCode string ?????????????????????
     * @return User
     * @throws Exception
     */
    function updatePhone($phone, $phoneCode, $oldPhone = null, $oldPhoneCode = null)
    {
        $param = (new UpdatePhoneParam($phone, $phoneCode))->withOldPhone($oldPhone)->withOldPhoneCode($oldPhoneCode);
        $user = $this->request($param->createRequest());
        $this->accessToken = $user->token ?: $this->accessToken;
        return $user;
    }

    /**
     * ????????????
     *
     * @param $email string ??????
     * @param $emailCode string ???????????????
     * @param $oldEmail string ?????????
     * @param $oldEmailCode string ??????????????????
     * @return User
     * @throws Exception
     */
    function updateEmail($email, $emailCode, $oldEmail = null, $oldEmailCode = null)
    {
        $param = (new UpdateEmailParam($email, $emailCode))->withOldEmail($oldEmail)->withOldEmailCode($oldEmailCode);
        $user = $this->request($param->createRequest());
        $this->accessToken = $user->token ?: $this->accessToken;
        return $user;
    }

    /**
     * ?????? access token
     *
     * @return RefreshToken
     * @throws Exception
     */
    function refreshToken()
    {
        $param = new RefreshTokenParam();
        return $this->request($param->createRequest());
    }

    /**
     * ???????????????
     *
     * @param $phone string ?????????
     * @param $phoneCode string ??????????????????
     * @return User
     * @throws Exception
     */
    function bindPhone($phone, $phoneCode)
    {
        $param = new BindPhoneParam($phone, $phoneCode);
        $user = $this->request($param->createRequest());
        $this->accessToken = $user->token ?: $this->accessToken;
        return $user;
    }

    /**
     * ??????????????????
     *
     * @return User
     * @throws Exception
     */
    function unBindPhone()
    {
        $param = new UnbindPhoneParam();
        $user = $this->request($param->createRequest());
        $this->accessToken = $user->token ?: $this->accessToken;
        return $user;
    }

    /**
     * ??????
     *
     * @throws Exception
     */
    function logout()
    {
        $param = new UpdateUserParam((new UpdateUserInput())->withTokenExpiredAt('0'));
        $this->request($param->createRequest());
        $this->accessToken = '';
    }

    /**
     * ??????????????????????????????????????????
     *
     * @return UserDefinedData[]
     * @throws Exception
     */
    function listUdv()
    {
        $user = $this->getCurrentUser();
        $param = new UdvParam(UdfTargetType::USER, $user->id);
        return $this->request($param->createRequest());
    }

    /**
     * ?????????????????????
     *
     * @param $key string ??????????????? key
     * @param $value string ??????????????????
     * @return CommonMessage
     * @throws Exception
     */
    function setUdv($key, $value)
    {
        $user = $this->getCurrentUser();
        $param = new SetUdvParam(UdfTargetType::USER, $user->id, $key, json_encode($value));
        return $this->request($param->createRequest());
    }

    /**
     * ?????????????????????
     * -
     *
     * @param $key string ??????????????? key
     * @return CommonMessage
     * @throws Exception
     */
    function removeUdv($key)
    {
        $user = $this->getCurrentUser();
        $param = new RemoveUdvParam(UdfTargetType::USER, $user->id, $key);
        return $this->request($param->createRequest());
    }


    /**
     * ??????????????????
     *
     * @param  $password string ????????????????????????
     * @return void
     */
    function checkPasswordStrength($password)
    {
        if (!isset($password)) {
            throw new Exception("???????????????");
        }
        $param = new CheckPasswordStrengthParam($password);
        return $this->request($param->createRequest());
    }

    // ?????????
    function updateAvatar($src)
    {
        if (!isset($set)) {
            throw new Exception("??????????????????????????????");
        } else {
            $id = $this->user->id;
            if ($id) {
            } else {
                throw new Exception("?????????????????????");
            }
        }
    }

    function linkAccount($options)
    {
        if (!isset($options)) {
            throw new Exception("??????????????????????????????");
        }
        $data = new \stdClass;
        $data->primaryUserToken = $options->primaryUserToken;
        $data->secondaryUserToken = $options->secondaryUserToken;
        $res = $this->httpPost("/api/v2/users/link", $data);
        return $res;
    }

    function listOrg()
    {
        return $this->httpGet('/api/v2/users/me/orgs');
    }

    function loginByLdap($username, $password, $options = "")
    {
        if (!isset($username, $password)) {
            throw new Exception("????????????????????????");
        } else {
            if (!isset($options)) {
                $options = new stdClass;
            }
            $_ = new stdClass();
            $_->username = $username;
            $_->password = $password;
            $user = $this->httpPost('/api/v2/ldap/verify-user', $_);
            $this->setAccessToken($user->$user->token ?: $this->accessToken);
            return $user;
        }
    }

    function loginByAd($username, $password)
    {
        $hostName = parse_url($this->options['host']);
        if (!$hostName) {
            throw new Exception("?????? ??????");
        } else {
            $hostName = $hostName['host'];
        }
        $firstLevelDomain = array_slice(explode(".", $hostName), 1);
        $websocketHost = $this->options["websocketHost"] || `https://ws.${firstLevelDomain}`;
        $api = $websocketHost . "/api/v2/ad/verify-user";
        $_ = new stdClass;
        $_->username = $username;
        $_->password = $password;
        $user = $this->httpPost($api, $_, true);
        $this->setAccessToken($user->$user->token ?: $this->accessToken);
        return $user;
    }

    function computedPasswordSecurityLevel($password)
    {
        if (!isset($password) && !is_string($password)) {
            throw new Exception("??????????????????");
        }
        $highLevel = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[^]{12,}$/g";
        $middleLevel = "/^(?=.*[a-zA-Z])(?=.*\d)[^]{8,}$/g";
        if (preg_match_all($middleLevel, $password)) {
            return $this->PasswordSecurityLevel['HIGH'];
        }
        if (preg_match_all($highLevel, $password)) {
            return $this->PasswordSecurityLevel['MIDDLE'];
        }
        return $this->PasswordSecurityLevel['LOW'];
    }

    function getSecurityLevel()
    {
        $res = $this->httpGet('/api/v2/users/me/security-level');
        return $res;
    }

    function listAuthorizedResources($namespace, $opts = [])
    {
        $resourceType = null;
        if (count($opts) > 0) {
            $resourceType = $opts['resourceType'];
        }
        $user = $this->getCurrentUser();
        if (!$user) {
            throw new Exception("?????????????????????");
        }
        if (!isset($namespace) && !is_string($namespace)) {
            throw new Exception("namespace ?????????");
        }
        $param = (new ListUserAuthorizedResourcesParam($user->id))->withNamespace($namespace)->withResourceType($resourceType);
        return formatAuthorizedResources($this->request($param->createRequest()));
    }

    public function _generateTokenRequest($params)
    {
        $p = http_build_query($params);
        return $p;
    }

    function _getAccessTokenByCodeWithClientSecretPost(string $code)
    {
        $data = array(
            'client_id' => $this->options->appId,
            'client_secret' => $this->options->secret,
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->options->redirectUri
        );
        $api = '';
        if ($this->options->protocol === 'oidc') {
            $api = '/oidc/token';
        } else if ($this->options->protocol === 'oauth') {
            $api = '/oauth/token';
        }
        $req = new Request('POST', $api, [
            'body' => $data,
            'headers' => array_merge(
                $this->getOidcHeaders(),
                [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ]
            )
        ]);
        $tokenSet = $this->naiveHttpClient->send($req);
        return $tokenSet;
    }

    function _generateBasicAuthToken(string $appId = "", string $secret = "")
    {
        if ($appId) {
            $id = $appId;
        } else {
            $id = $this->options->appId;
        }
        if ($secret) {
            $secret = $secret;
        } else {
            $secret = $this->options->secret;
        }
        $token = 'Basic ' . base64_encode($id . ":" . $secret);
        return $token;
    }

    function _getAccessTokenByCodeWithClientSecretBasic(string $code)
    {
        $api = '';
        if ($this->options->protocol === 'oidc') {
            $api = '/oidc/token';
        } else if ($this->options->protocol === 'oauth') {
            $api = '/oauth/token';
        }
        $qstr = $this->_generateTokenRequest(
            [
                'client_id' => $this->options->appId,
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $this->options->redirectUri,
            ]
        );
        $response = $this->naiveHttpClient->request('POST', $api, [
            "headers" =>
            array_merge($this->getOidcHeaders(), [
                "Authorization" => $this->_generateBasicAuthToken(),
                'Content-Type' => 'application/x-www-form-urlencoded',
            ]),
            "body" => $qstr,
        ]);
        $body =
            $response->getBody();
        $stringBody = (string) $body;
        return json_decode($stringBody);
    }

    function _getAccessTokenByCodeWithNone(string $code)
    {
        $api = '';
        if ($this->options->protocol === 'oidc') {
            $api = '/oidc/token';
        } else if ($this->options->protocol === 'oauth') {
            $api = '/oauth/token';
        }
        $qstr = $this->_generateTokenRequest(
            [
                "client_id" => $this->options->appId,
                "grant_type" =>
                'authorization_code',
                "code" => $code,
                "client_secret"=> $this->options->secret,
                "redirect_uri" => $this->options->redirectUri
            ]
        );
        $response = $this->naiveHttpClient->request("POST", $api, [
            "body" => $qstr,
            "headers" =>
            array_merge($this->getOidcHeaders(), [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ]),
        ]);
        $body =
            $response->getBody();
        $stringBody = (string) $body;
        return json_decode($stringBody);
    }

    function getAccessTokenByCode(string $code)
    {
        if (
            (!isset($this->options->secret) ||
            !isset($this->options->tokenEndPointAuthMethod)) ||
            (!$this->options->secret &&
            $this->options->tokenEndPointAuthMethod !== 'none')
        ) {
            throw new Error('??????????????? AuthenticationClient ????????? appId ??? secret ??????');
        }
        if (isset($this->options->tokenEndPointAuthMethod) && $this->options->tokenEndPointAuthMethod === 'client_secret_post') {
            return $this->_getAccessTokenByCodeWithClientSecretPost($code);
        }
        if (isset($this->options->tokenEndPointAuthMethod)  && $this->options->tokenEndPointAuthMethod === 'client_secret_basic') {
            return $this->_getAccessTokenByCodeWithClientSecretBasic($code);
        }
        if (isset($this->options->tokenEndPointAuthMethod)  && $this->options->tokenEndPointAuthMethod === 'none') {
            return $this->_getAccessTokenByCodeWithNone($code);
        }
    }

    function getAccessTokenByClientCredentials(string $scope, array $options)
    {
        if (!isset($scope) || $scope) {
            throw new Error(
                '????????? scope ????????????????????????https://docs.authing.cn/v2/guides/authorization/m2m-authz.html'
            );
        }
        if (count($options) === 0) {
            throw new Error(
                '?????????????????????????????? { accessKey: string, accessSecret: string }??????????????????https://docs.authing.cn/v2/guides/authorization/m2m-authz.html'
                // '??????????????? AuthenticationClient ????????? appId ??? secret ??????????????????????????????????????? { accessKey: string, accessSecret: string }??????????????????https://docs.authing.cn/v2/guides/authorization/m2m-authz.html'
            );
        }
        $this->options->accessKey
            ? $accessKey = $this->options->accessKey : $accessKey = $this->options->appId;
        $this->options->accessSecret
            ? $accessSecret = $this->options->accessSecret : $accessSecret = $this->options->secret;
        $qstr = $this->_generateTokenRequest([
            "client_id" => $accessKey,
            "client_secret" => $accessSecret,
            "grant_type" => 'client_credentials',
            "scope" => $scope
        ]);
        $api = '';
        if ($this->options->protocol === 'oidc') {
            $api = '/oidc/token';
        } else if ($this->options->protocol === 'oauth') {
            $api = '/oauth/token';
        }
        $response = $this->naiveHttpClient->request('POST', $api, [
            "body" => $qstr,
            "headers" => array_merge(
                $this->getOidcHeaders(),
                [
                    "Content-Type" => 'application/x-www-form-urlencoded',
                ]
            )
        ]);
        $body =
            $response->getBody();
        $stringBody = (string) $body;
        return json_decode($stringBody);
    }

    function getUserInfoByAccessToken(string $accessToken)
    {
        $api = '';
        if ($this->options->protocol === 'oidc') {
            $api = '/oidc/me';
        } else if ($this->options->protocol === 'oauth') {
            $api = '/oauth/me';
        }
        $response = $this->naiveHttpClient->request("POST", $api, [
            'headers' => array_merge(
                $this->getOidcHeaders(),
                [
                    'Authorization' => 'Bearer ' . $accessToken,
                ]
            )
        ]);
        $body =
            $response->getBody();
        $stringBody = (string) $body;
        return json_decode($stringBody);
    }

    function getOidcHeaders()
    {
        $SDK_VERSION = "4.1.0";
        return [
            'x-authing-sdk-version' => 'php:' . $SDK_VERSION,
            'x-authing-userpool-id' => (isset($this->options->userPoolId) ? $this->options->userPoolId : ""),
            'x-authing-request-from' => (isset($this->options->requestFrom) ? $this->options->requestFrom : 'sdk'),
            'x-authing-app-id' => (isset($this->options->appId) ? $this->options->appId : '')
        ];
    }
}
