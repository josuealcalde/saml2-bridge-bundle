<?php

/**
 * Copyright 2017 Adactive SAS
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace AdactiveSas\Saml2BridgeBundle\SAML2\Builder;


use RobRichards\XMLSecLibs\XMLSecurityKey;
use SAML2\Assertion;
use SAML2\Constants;
use SAML2\XML\saml\SubjectConfirmation;
use SAML2\XML\saml\SubjectConfirmationData;

class AssertionBuilder
{
    /**
     * @var Assertion
     */
    protected $assertion;

    /**
     * @var \DateTime
     */
    protected $issueInstant;

    /**
     * AssertionBuilder constructor.
     * @param \DateTime|null $issueInstant
     * @throws \Exception
     */
    public function __construct(\DateTime $issueInstant = null)
    {
        $this->assertion = new Assertion();

        $this->issueInstant = $issueInstant === null ? new \DateTime('now', new \DateTimeZone('UTC')) : $issueInstant;

        $this->assertion->setNotBefore($this->issueInstant->getTimestamp());
        $this->assertion->setIssueInstant($this->issueInstant->getTimestamp());

        // Add default bearer confirmation
        $confirmation = new SubjectConfirmation();
        $confirmation->Method = Constants::CM_BEARER;

        $confirmationData = new SubjectConfirmationData();

        $confirmation->SubjectConfirmationData = $confirmationData;

        $this->assertion->setSubjectConfirmation([$confirmation]);
    }

    /**
     * @return Assertion
     */
    public function getAssertion()
    {
        return $this->assertion;
    }

    /**
     * @return \DateTime|null
     */
    public function getIssueInstant()
    {
        return $this->issueInstant;
    }

    /**
     * @param \DateInterval $interval
     * @return $this
     */
    public function setNotBefore(\DateInterval $interval = null)
    {
        $beforeTime = clone $this->issueInstant;
        if($interval !== null) {
            $beforeTime->sub($interval);
        }

        $this->assertion->setNotBefore($beforeTime->getTimestamp());

        if ($interval !== null) {
            /** @var SubjectConfirmation $confirmation */
            $confirmation = $this->assertion->getSubjectConfirmation()[0];
            $confirmation->SubjectConfirmationData->NotBefore = $beforeTime->getTimestamp();
            $this->assertion->setSubjectConfirmation([$confirmation]);
        }

        return $this;
    }

    /**
     * @param \DateInterval $interval
     * @return $this
     */
    public function setNotOnOrAfter(\DateInterval $interval)
    {
        $endTime = clone $this->issueInstant;
        $endTime->add($interval);

        $this->assertion->setNotOnOrAfter($endTime->getTimestamp());

        $confirmation = $this->assertion->getSubjectConfirmation()[0];
        $confirmation->SubjectConfirmationData->NotOnOrAfter = $endTime->getTimestamp();
        $this->assertion->setSubjectConfirmation([$confirmation]);

        return $this;
    }

    /**
     * @param \DateInterval $interval
     * @return $this
     */
    public function setSessionNotOnOrAfter(\DateInterval $interval)
    {
        $sessionEndTime = clone $this->issueInstant;
        $sessionEndTime->add($interval);

        $this->assertion->setSessionNotOnOrAfter($sessionEndTime->getTimestamp());
        $confirmation = $this->assertion->getSubjectConfirmation()[0];
        $confirmation->SubjectConfirmationData->NotOnOrAfter = $sessionEndTime->getTimestamp();
        $this->assertion->setSubjectConfirmation([$confirmation]);

        return $this;
    }

    /**
     * @param string|null $inResponseTo
     * @return $this
     */
    public function setInResponseTo($inResponseTo)
    {
        $confirmation = $this->assertion->getSubjectConfirmation()[0];
        /** @var SubjectConfirmation $confirmation */
        $confirmation->SubjectConfirmationData->InResponseTo = $inResponseTo;

        return $this;
    }

    /**
     * @param string $method
     * @return $this
     */
    public function setConfirmationMethod($method = Constants::CM_BEARER)
    {
        $confirmation = $this->assertion->getSubjectConfirmation()[0];
        /** @var SubjectConfirmation $confirmation */
        $confirmation->Method = $method;

        return $this;
    }

    /**
     * @param string|null $recipient
     * @return $this
     */
    public function setRecipient($recipient)
    {
        $confirmation = $this->assertion->getSubjectConfirmation()[0];
        /** @var SubjectConfirmation $confirmation */
        $confirmation->SubjectConfirmationData->Recipient = $recipient;

        return $this;
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function setAttributes(array $attributes)
    {
        $this->assertion->setAttributes($attributes);

        return $this;
    }

    /**
     * @param string $nameFormat
     *
     * @return $this
     */
    public function setAttributesNameFormat($nameFormat = Constants::NAMEFORMAT_UNSPECIFIED){
        $this->assertion->setAttributeNameFormat($nameFormat);

        return $this;
    }

    /**
     * @param string $name
     * @param $value
     * @return AssertionBuilder
     */
    public function setAttribute($name, $value)
    {
        $attributes = $this->assertion->getAttributes();
        if (!is_array($value)) {
            $value = [$value];
        }
        $attributes[$name] = $value;

        return $this->setAttributes($attributes);
    }

    /**
     * @param string $value
     * @param string $format
     * @param null|string $nameQualifier
     * @param null|string $spNameQualifier
     * @return $this
     */
    public function setNameId($value, $format = null, $nameQualifier = null, $spNameQualifier = null)
    {
        $nameId = [
            'Value' => $value,
            'Format' => $format,
            'SPNameQualifier' => $spNameQualifier,
            'NameQualifier' => $nameQualifier,
        ];

        $this->assertion->setNameId($nameId);

        return $this;
    }

    /**
     * @return $this
     */
    public function setSubjectConfirmation($method = Constants::CM_BEARER, $inResponseTo, \DateInterval $notOnOrAfter, $recipient) {
        $subjectConfirmationData = new SubjectConfirmationData();
        $subjectConfirmationData->InResponseTo = $inResponseTo;

        $endTime = clone $this->issueInstant;
        $endTime->add($notOnOrAfter);
        $subjectConfirmationData->NotOnOrAfter = $endTime->getTimestamp();

        $subjectConfirmationData->Recipient = $recipient;

        $subjectConformation = new SubjectConfirmation();
        $subjectConformation->Method = $method;
        $subjectConformation->SubjectConfirmationData = $subjectConfirmationData;
        $this->assertion->setSubjectConfirmation([$subjectConformation]);

        return $this;
    }
    /**
     * @return $this
     */
    public function setAuthnContext($authnContext = Constants::AC_PASSWORD) {
        $this->assertion->setAuthnContextClassRef($authnContext);

        return $this;
    }

    /**
     * @param $issuer
     * @return $this
     */
    public function setIssuer($issuer)
    {
        $this->assertion->setIssuer($issuer);

        return $this;
    }

    public function setValidAudiences(array $validAudiences = NULL)
    {
        $this->assertion->setValidAudiences($validAudiences);

        return $this;
    }

    /**
     * @param XMLSecurityKey $privateKey
     * @param XMLSecurityKey $publicCert
     */
    public function sign(XMLSecurityKey $privateKey, XMLSecurityKey $publicCert)
    {
        $element = $this->assertion;
        $element->setSignatureKey($privateKey);
        $element->setCertificates([$publicCert->getX509Certificate()]);
    }
}
