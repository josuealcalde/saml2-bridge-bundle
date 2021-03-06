<?php

namespace AdactiveSas\Saml2BridgeBundle\SAML2\State;

use SAML2\Constants;
use SAML2\Request;
use Symfony\Component\HttpFoundation\Response;

class SamlState
{
    const STATE_INITIAL = "initial";

    const STATE_SSO_STARTED = "sso_start";
    const STATE_SSO_AUTHENTICATING_START = "sso_authenticating_start";
    const STATE_SSO_AUTHENTICATING_FAILED = "sso_authenticating_failed";
    const STATE_SSO_AUTHENTICATING_SUCCESS = "sso_authenticating_success";
    const STATE_SSO_RESPONDING = "sso_responding";

    const STATE_SLS_STARTED = "sls_start";
    const STATE_SLS_DISPATCH_START = "sls_dispatch_start";
    const STATE_SLS_DISPATCH_END = "sls_dispatch_end";
    const STATE_SLS_PROPAGATE_START = "sls_propagate_start";
    const STATE_SLS_PROPAGATE_END = "sls_propagate_end";
    const STATE_SLS_RESPONDING = "sls_responding";

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var string
     */
    protected $state;

    /**
     * @var array
     */
    protected $serviceProvidersIds;

    /**
     * @var string
     */
    protected $userName;

    /**
     * @var Response|null
     */
    protected $originalLogoutResponse;

    /**
     * @var int
     */
    protected $loginRetryCount;

    /**
     * @var null|string
     */
    protected $authnContext = Constants::AC_UNSPECIFIED;

    /**
     * SamlState constructor.
     */
    public function __construct()
    {
        $this->state = self::STATE_INITIAL;
        $this->serviceProvidersIds = [];
        $this->loginRetryCount = 0;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     * @return $this
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function setRequest(Request $request = null)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return array
     */
    public function getServiceProvidersIds()
    {
        return $this->serviceProvidersIds;
    }

    /**
     * @param array $serviceProvidersIds
     * @return $this
     */
    public function setServiceProvidersIds(array $serviceProvidersIds)
    {
        $this->serviceProvidersIds = $serviceProvidersIds;
        return $this;
    }

    /**
     * @param $id
     * @return $this
     */
    public function addServiceProviderId($id)
    {
        if(!$id){
            throw new \RuntimeException();
        }

        if (!$this->hasServiceProviderId($id)) {
            $this->serviceProvidersIds[] = $id;
        }

        return $this;
    }

    /**
     * @param $id
     * @return $this
     */
    public function removeServiceProviderId($id)
    {
        $key = array_search($id, $this->serviceProvidersIds);

        if ($key !== false) {
            unset($this->serviceProvidersIds[$key]);

            $this->serviceProvidersIds = array_values($this->serviceProvidersIds);
        }

        return $this;
    }

    /**
     * @param $id
     * @return bool
     */
    public function hasServiceProviderId($id)
    {
        return in_array($id, $this->serviceProvidersIds);
    }

    /**
     * @return bool
     */
    public function hasServiceProviderIds()
    {
        return !empty($this->serviceProvidersIds);
    }

    /**
     * @return mixed
     */
    public function popServiceProviderIds(){
        return array_pop($this->serviceProvidersIds);
    }

    /**
     * @return string|null
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @param string|null $userName
     * @return SamlState
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * @return null|Response
     */
    public function getOriginalLogoutResponse()
    {
        return $this->originalLogoutResponse;
    }

    /**
     * @param null|Response $originalLogoutResponse
     * @return SamlState
     */
    public function setOriginalLogoutResponse(Response $originalLogoutResponse = null)
    {
        $this->originalLogoutResponse = $originalLogoutResponse;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getAuthnContext()
    {
        return $this->authnContext;
    }

    /**
     * @param null|string $authnContext
     * @return SamlState
     */
    public function setAuthnContext($authnContext)
    {
        $this->authnContext = $authnContext;
        return $this;
    }

    /**
     * @return int
     */
    public function getLoginRetryCount()
    {
        return $this->loginRetryCount;
    }

    /**
     * @param int $loginRetryCount
     * @return SamlState
     */
    public function setLoginRetryCount($loginRetryCount)
    {
        $this->loginRetryCount = $loginRetryCount;
        return $this;
    }

    /**
     * @return SamlState
     */
    public function incrementLoginRetryCount()
    {
        $this->loginRetryCount++;
        return $this;
    }

    /**
     * @return SamlState
     */
    public function resetLoginRetryCount()
    {
        $this->loginRetryCount = 0;
        return $this;
    }
}
