# CHANGELOG

## v0.14.1
### Fix
  - Fix problem with SP check when the login fails.

## v0.14.0
### Add
  - New events:
     - GetLogoutResponseEvent: Dispatched on Logout Response
    - ReceiveLogoutResponseEvent: Dispatched on Logout Request
    - LogoutTerminatedEvent: Dispatched after a Succcess Logout Event, but including service provider an state data.

### Fix
  - Fix logout errors when the user is not logged in, casued by type check

## v0.13.0
### Add
  - `ServiceProvider#description` property to give a human readable name to the Service Provider
  - `ServiceProvider#userAllowed` property or callable to check if a user can be logged in a Service Provider
  
## v0.12.0
### Add
  - Use bundle namespace in twig template
  - Add certificate to metadata XML
  
## v0.11.0
### Add
  - Updated code to SimpleSAML 3.0 new namespace
  
## v0.10.2
### Add
  - Updated to SimpleSAML 3.0
 
## v0.10.1
### Add
  - Symfony 4.4 support
  
## v0.10.0
### Add
  - Symfony 4 support
  - Able to set `assertionNotBeforeInterval` as `null` (fix for `SubjectConfirmationData notBefore`)
  - Allow to send an array of values for `attributes`
  - Add validAudiences in the SP configuration

### Fix
  - Missing Content-type xml on metadata response
  - SubjectConfirmationData notBefore must be null for Bearer confirmation
  - `AssertionBuilder.setNotBefore` should set `NotBefore` subjectConfirmationData and not `NotOnOrAfter`

## v0.9.1

### Fix
  - Dev dependencies to fix Travis build

## v0.9.0

### Add
  - `ServiceProvider#assertionNotBeforeInterval` property to customize assertion validity
  - `ServiceProvider#assertionNotOnOrAfterInterval` property to customize assertion validity
  - `ServiceProvider#assertionSessionNotOnOrAfterInterval` property to customize assertion validity

## v0.8.1

### Fix
  - Prevent throwing exception on the `HostedIdentityProviderProcessor::onKernelResponse` when there is no current state.
  - Unit tests

## v0.8.0

### Add
  - receiving POST binding request
  - NewRelic example
  - Single sign-on using `HostedIdentityProviderProcessor::processSingleSignOn` now supports GET and POST requests.
  - Single logout using `HostedIdentityProviderProcessor::processSingleLogoutService` now supports GET and POST messages.
  
## Fix
  - remove dependency of "roave/security-advisories" to allow require without putting minimum stability dev (#10)[https://github.com/AdactiveSAS/saml2-bridge-bundle/issues/10]
  
## Deprecated
  - `\AdactiveSas\Saml2BridgeBundle\Entity\IdentityProvider::getSsoBinding` was removed, overwriting this method have no
more effects.
  - `\AdactiveSas\Saml2BridgeBundle\Entity\IdentityProvider::getSlsBinding` was removed, overwriting this method have no
more effects.
  
## v0.7.1

### Fix
  - Travis test by increasing php memory limit

## v0.7.0

### Add
  - Default Logger into `adactive_sas_saml2_bridge.processor.hosted_idp` service
  - ServiceProvider option `maxRetryLogin` to setup the number of login retry in case of errors. The default is `0` to 
  keep retro-compatibility

### Fix
  - SLS initiated by IDP
  - composer 

